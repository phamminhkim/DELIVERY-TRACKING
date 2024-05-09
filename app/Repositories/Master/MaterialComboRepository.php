<?php

namespace App\Repositories\Master;

use App\Models\Master\MaterialCombo;
use App\Models\Master\CustomerGroup;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\Excel\ExcelExtractor;

class MaterialComboRepository extends RepositoryAbs
{
    public function getAll($is_minified)
    {
        try {
            $query = MaterialCombo::query();
            if ($this->request->filled('search')) {
                $query = $query->search($this->request->search);
                $query->limit(200);
            }
            if ($this->request->filled('sap_codes')) {
                $query = $query->whereIn('sap_code', $this->request->sap_codes);
            }
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            if ($is_minified) {
                $query->select('id', 'sap_code', 'name', 'bar_code');
            }

            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                if (!is_array($customer_group_ids)) {
                    $customer_group_ids = explode(',', $customer_group_ids);
                }
                $query->whereIn('customer_group_id', $customer_group_ids);
            }
            $query->with([
                'customer_group' => function ($query) {
                    $query->select(['id', 'name']);
                },
            ]);

            $perPage = $this->request->filled('per_page') ? $this->request->per_page : 500;
            $material_combo = $query->paginate($perPage, ['*'], 'page', $this->request->page);

            if ($this->request->filled('search') && $material_combo->isEmpty()) {
                $material_combo = $query->paginate($perPage, ['*'], 'page', $this->request->page);
            }

            $result = [
                'data' => $material_combo->items(),
                'per_page' => $material_combo->perPage(),
            ];

            $result['paginate'] = [
                'current_page' => $material_combo->currentPage(),
                'last_page' => $material_combo->lastPage(),
                'total' => $material_combo->total(),
            ];

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createMaterialComboFormExcel()
{
    try {
        $validator = Validator::make($this->data, [
            'file' => 'required|mimes:xlsx,xls',
        ], [
            'file.required' => 'File là bắt buộc.',
            'file.mimes' => 'File không đúng định dạng.',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->all();
            return false;
        } else {
            $file = $this->request->file('file');
            $excel_extractor = new ExcelExtractor();
            $data = $excel_extractor->extractData($file);
            $template_structure = [
                'customer_group_name' => 0,
                'sap_code' => 1,
                'name' => 2,
                'bar_code' => 3,
                'is_active' => 4, // Thêm trường is_active vào cấu trúc mẫu
            ];
            $result = [];
            $existing_combos = [];

            foreach ($data as $row) {
                $sap_code = $row[$template_structure['sap_code']];
                $name = $row[$template_structure['name']];
                $bar_code = $row[$template_structure['bar_code']];
                $is_active = $row[$template_structure['is_active']];
                $customer_group_names = explode(',', $row[$template_structure['customer_group_name']]);

                foreach ($customer_group_names as $customer_group_name) {
                    $customer_group = CustomerGroup::where('name', $customer_group_name)->first();

                    if (!$customer_group) {
                        $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $customer_group_name;
                        continue;
                    }

                    $existing_combo = MaterialCombo::where('sap_code', $sap_code)
                        ->where('customer_group_id', $customer_group->id)
                        ->first();

                    if ($existing_combo) {
                        // Bộ mã đã tồn tại, cập nhật thông tin
                        $existing_combo->name = $name;
                        $existing_combo->bar_code = $bar_code;
                        $existing_combo->is_active = ($is_active === null) ? 1 : 0;
                        $existing_combo->save();

                        $result[] = $existing_combo;
                    } else {
                        // Tạo mới bộ mã
                        $new_material_combo = MaterialCombo::create([
                            'customer_group_id' => $customer_group->id,
                            'sap_code' => $sap_code,
                            'name' => $name,
                            'bar_code' => $bar_code,
                            'is_active' => ($is_active === null) ? 1 : 0,
                        ]);

                        $result[] = $new_material_combo;
                    }
                }
            }
            return [
                "created_list" => $result,
                "errors" => $this->errors
            ];
        }
    } catch (\Exception $exception) {
        $this->message = $exception->getMessage();
        $this->errors = $exception->getTrace();
        return false;
    }
}
    public function store()
    {
        try {
            $validator = Validator::make(
                $this->data,
                [
                    'customer_group_id' => 'required',
                    'sap_code' => 'required|unique:material_combos,sap_code',
                    'bar_code' => 'string',
                    'name' => 'required',
                    'is_active' => 'in:0,1',

                ],
                [
                    'customer_group_id.required' => 'Mã SAP không được để trống',
                    'sap_code.required' => 'Mã SAP không được để trống',
                    'sap_code.unique' => 'Mã SAP đã tồn tại',
                    'bar_code.string' => 'Tên không được để trống',
                    'name.required' => 'Tên không được để trống',
                    'is_active.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',

                ]
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $customer_group = CustomerGroup::find($this->data['customer_group_id']);
                if (!$customer_group) {
                    $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $this->data['customer_group_id'];
                    return false;
                }

                $material_combo = MaterialCombo::create($this->data);
                return $material_combo;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function update($id)
    {
        try {
            $material_combo = MaterialCombo::where('id', $id)->firstOrFail();

            $validator = Validator::make($this->data, [
                'customer_group_id' => 'integer',
                'sap_code' => 'string',
                'bar_code' => 'string',
                'name' => 'string',
                'is_active' => 'in:0,1',

            ], [
                'customer_group_id.integer' => 'Nhóm khách hàng phải là số nguyên.',
                'sap_code.string' => 'Mã sản phẩm phải là chuỗi.',
                'bar_code.string' => 'Mã sản phẩm phải là chuỗi.',
                'name.string' => 'Mã sản phẩm phải là chuỗi.',
                'is_active.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',

            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }

            $customer_group = CustomerGroup::find($this->data['customer_group_id']);
            if (!$customer_group && $this->data['customer_group_id']) {
                $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $this->data['customer_group_id'];
                return false;
            }
            $material_combo = MaterialCombo::findOrFail($id);

            // Tiếp tục với việc cập nhật dữ liệu
            $material_combo->update($this->data);
            return $material_combo;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }

    public function destroy($request, $id)
    {
        try {
            $material_combo = MaterialCombo::find($id);
            $material_combo->delete();
            return $material_combo;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
