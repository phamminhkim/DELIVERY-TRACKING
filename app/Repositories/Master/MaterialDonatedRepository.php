<?php

namespace App\Repositories\Master;

use App\Models\Master\MaterialDonated;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;



class MaterialDonatedRepository extends RepositoryAbs
{
    public function getAll($is_minified, $request)
    {
        try {
            $query = MaterialDonated::query();

            if ($request->filled('search')) {
                $query->limit(50);
            }
            if ($request->filled('sap_codes')) {
                $query->whereIn('sap_code', $request->sap_codes);
            }

            if ($request->filled('ids')) {
                $query->whereIn('id', $request->ids);
            }

            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }

            if ($request->filled('search') && $request->search != null && $request->search != 'undefined') {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('sap_code', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%");
                });
            }
            if ($is_minified) {
                $query->select('id', 'name', 'sap_code');
            }

            $perPage = $request->filled('per_page') ? $request->per_page : 500;
            $material_donateds = $query->paginate($perPage, ['*'], 'page', $request->page);

            if ($request->filled('search') && $material_donateds->isEmpty()) {
                $material_donateds = $query->paginate($perPage, ['*'], 'page', $request->page);
            }

            $result = [
                'data' => $material_donateds->items(),
                'per_page' => $material_donateds->perPage(),
            ];

            $result['paginate'] = [
                'current_page' => $material_donateds->currentPage(),
                'last_page' => $material_donateds->lastPage(),
                'total' => $material_donateds->total(),
            ];

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createMaterialDonatedFormExcel()
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
                    'sap_code' => 0,
                    'name' => 1,
                    'is_active' => 2, // Index of the 'is_active' column in the Excel file
                ];
                $result = collect([]);

                foreach ($data as $row) {
                    $is_active = strtolower($row[$template_structure['is_active']]);

                    // Chuyển đổi giá trị 'is_active' thành 1 nếu chuỗi là null, ngược lại thành 0
                    $is_active = ($is_active != null) ? 0 : 1;
                    $material_donated = MaterialDonated::updateOrCreate(
                        [
                            'sap_code' => $row[$template_structure['sap_code']],
                            'name' => $row[$template_structure['name']],
                        ],
                        [
                            'is_active' => $is_active,
                        ]
                    );

                    $result->push($material_donated);
                }

                return [
                    'created_list' => $result,
                    'errors' => $this->errors,
                ];
            }
        } catch (\Exception $exception) {
            DB::rollBack();
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
                    'sap_code' => 'required|unique:material_donateds,sap_code',
                    'name' => 'required',
                    'is_active' => 'in:0,1',
                ],
                [
                    'sap_code.required' => 'Mã SAP không được để trống',
                    'sap_code.unique' => 'Mã SAP đã tồn tại',
                    'name.required' => 'Tên không được để trống',
                    'is_active.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',
                ]
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $material_donated_data = [
                    'sap_code' => $this->data['sap_code'],
                    'name' => $this->data['name'],
                ];
                if (isset($this->data['is_active'])) {
                    $material_donated_data['is_active'] = (int) $this->data['is_active'];
                }

                $materialDonated = MaterialDonated::create($material_donated_data);



                return $materialDonated;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function update($request, $id)
    {
        try {
            $validator = Validator::make(
                $this->data,
                [
                    'sap_code' => 'integer',
                    'name' => 'required',
                    'is_active' => 'in:0,1', // Thay đổi quy tắc kiểm tra cho check_pc

                ],
                [
                    'sap_code.integer' => 'Mã SAP phải là số nguyên',
                    'name.required' => 'Tên không được để trống',
                    'is_active.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',

                ]
            );

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $materialDonated => $validator) {
                    if ($errors->has($materialDonated)) {
                        $this->errors[$materialDonated] = $errors->first($materialDonated);
                        return false;
                    }
                }
            } else {

                $materialDonated = MaterialDonated::findOrFail($id);
                $materialDonated->fill([
                    'sap_code' => $this->data['sap_code'],
                    'name' => $this->data['name'],
                ]);

                if (isset($this->data['is_active'])) {
                    $materialDonated->is_active = (int) $this->data['is_active'];
                }
                $materialDonated->save();

                return $materialDonated;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function destroy($request, $id)
    {
        try {
            $material_donated = MaterialDonated::find($id);
            $material_donated->delete();
            return $material_donated;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
