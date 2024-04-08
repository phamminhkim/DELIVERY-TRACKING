<?php

namespace App\Repositories\Master;

use App\Models\Master\SapMaterial;
use App\Models\Master\SapUnit;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Company;


class SapMaterialRepository extends RepositoryAbs
{
    public function getAvailableSapMaterials($is_minified, $request)
    {
        try {
            $query = SapMaterial::query();

            // Tối ưu hóa dữ liệu theo dạng phân trang
            $result = array();
            $sapMaterials = $query->paginate(200, ['*'], 'page', $request->page);
            $result['sap_material_mappings'] = $sapMaterials->items();
            $result['paginate'] = [
                'current_page' => $sapMaterials->currentPage(),
                'last_page' => $sapMaterials->lastPage(),
                'total' => $sapMaterials->total(),
            ];

            $is_searching = false;
            if ($request->filled('search')) {
                $query->search($request->search);
                $query->with(['unit']);
                $query->limit(50);
                $is_searching = true;
            }
            if ($request->filled('bar_codes')) {
                $query->whereIn('bar_code', $request->bar_codes);
            }
            if ($request->filled('unit_ids')) {
                $query->whereIn('unit_id', $request->unit_ids);
            }
            if ($request->filled('ids')) {
                $query->whereIn('id', $request->ids);
            }
            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }

            if ($is_minified) {
                $query->select('id', 'name', 'sap_code', 'unit_id', 'bar_code');
            }
            $query->with([
                'unit' => function ($query) {
                    $query->select(['id', 'unit_code']);
                },
            ]);
            $sap_materials = $query->get();
            if ($is_searching) {
                $sap_materials_clone = clone $sap_materials;
                $sap_materials_clone->where('code', $request->search);
                if (!$sap_materials_clone->count()) {
                    $sap_materials = $sap_materials_clone;
                }
            }
            return $sap_materials;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }


    public function createSapMaterialFormExcel()
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
                    'unit_code' => 1, // Thay đổi cột 'unit_id' thành 'unit_code'
                    'bar_code' => 2,
                    'name' => 3,
                ];
                $result = collect([]);

                foreach ($data as $row) {
                    $unit = SapUnit::where('unit_code', $row[$template_structure['unit_code']])->first();

                    if ($unit) {
                        $sapMaterial = SapMaterial::updateOrCreate(
                            [
                                'sap_code' => $row[$template_structure['sap_code']],
                                'unit_id' => $unit->id, // Sử dụng 'id' của bảng 'unit'
                            ],
                            [
                                'bar_code' => $row[$template_structure['bar_code']],
                                // Thêm các trường dữ liệu khác tương ứng
                            ]
                        );

                        $result->push($sapMaterial);
                    }
                }

                return array(
                    "created_list" => $result,
                    "errors" => $this->errors
                );
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function createNewSapMaterial()
    {
        try {
            $validator = Validator::make($this->data, [

                'sap_code' => 'required|string',
                'unit_id' => 'required|integer|exists:sap_units,id',
                'name' => 'required|string',
                'bar_code' => 'string',
            ], [
                'sap_code.required' => 'Yêu cầu nhập mã material.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                'sap_code.unique' => 'Mã material đã tồn tại.',
                'unit_id.integer' => 'Mã unit phải là chuỗi.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                'name.required' => 'Yêu cầu nhập tên material.',
                'name.string' => 'Tên material phải là chuỗi.',
                'bar_code.string' => 'Mã Barcode phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->toArray();
                return false;
            } else {
                $sapMaterial = SapMaterial::create([
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    'bar_code' => $this->data['bar_code'],
                    'name' => $this->data['name'],
                ]);

                return $sapMaterial;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }

    public function updateOrInsert()
    {
        $validator = Validator::make($this->data, [
            // '*.company_code' => 'required|string|exists:companies,code',
            '*.code' => 'required|string',
            '*.name' => 'required|string',
        ], [
            // '*.company_code.required' => 'Yêu cầu nhập mã công ty.',
            // '*.company_code.string' => 'Mã công ty phải là chuỗi.',
            // '*.company_code.exists' => 'Mã công ty không tồn tại.',
            '*.code.required' => 'Yêu cầu nhập mã material.',
            '*.code.string' => 'Mã material phải là chuỗi.',
            '*.name.required' => 'Yêu cầu nhập tên material.',
            '*.name.string' => 'Tên material phải là chuỗi.',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->all();
            return $this->errors;
        } else {
            try {
                $result = array(
                    'insert_count' => 0,
                    'update_count' => 0,
                    'skip_count' => 0,
                    'delete_count' => 0,
                    'error_count' => 0,
                );
                foreach ($this->data as $material) {
                    // $company_id = '';
                    $unit = SapUnit::where('unit_code', $material['unit_code'])->first();
                    if (!$unit) {
                        $unit = SapUnit::create(['unit_code' => $material['unit_code']]);
                    }
                    $exist_sap_material = SapMaterial::query()
                        ->where('sap_code', $material['code'])
                        ->where('unit_id', $unit->id)->first();

                    if ($material['status'] == "delete") {
                        $exist_sap_material->is_deleted = true;
                        $exist_sap_material->save();
                    } {
                        if ($exist_sap_material) {
                            if ($exist_sap_material->bar_code == "") {
                                $exist_sap_material->update([
                                    'name' => $material['name'],
                                    'bar_code' => $material['bar_code'],
                                ]);
                            } else {
                                $exist_sap_material->update([
                                    'name' => $material['name'],
                                    //  'bar_code' => $material['bar_code'],
                                ]);
                            }

                            $result['update_count']++;
                        } else {
                            SapMaterial::create([
                                'unit_id' => $unit->id,
                                'sap_code' => $material['code'],
                                'name' => $material['name'],
                                'bar_code' => $material['bar_code'], // Thêm trường mới
                            ]);
                            $result['insert_count']++;
                        }
                    }
                }
                return $result;
            } catch (\Exception $exception) {
                $this->message = $exception->getMessage();
                $this->errors = $exception->getTrace();
            }
        }
    }
    public function updateExistingSapMaterial($id)
    {
        try {
            $validator = Validator::make($this->data, [

                'sap_code' => 'string:sap_materials,sap_code',
                'unit_id' => 'integer|exists:sap_units,id',
                'bar_code' => 'string',
                'name' => 'required|string',
            ], [
                'sap_code.required' => 'Yêu cầu nhập mã SAP.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                //'sap_code.unique' => 'Mã material đã tồn tại.',
                'unit_id.integer' => 'Mã unit phải là chuỗi.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                'bar_code.string' => 'Mã Barcode phải là chuỗi.',
                'name.required' => 'Yêu cầu nhập tên SAP.',
                'name.string' => 'Tên SAP phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $SapMaterial => $validator) {
                    if ($errors->has($SapMaterial)) {
                        $this->errors[$SapMaterial] = $errors->first($SapMaterial);
                        return false;
                    }
                }
            } else {

                $sap_unit = SapUnit::find($this->data['unit_id']);
                if (!$sap_unit) {
                    $this->errors = 'Không tìm thấy mã sap_unit ' . $this->data['unit_id'];
                    return false;
                }

                $sapMaterial = SapMaterial::findOrFail($id);
                $sapMaterial->fill([
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    'bar_code' => $this->data['bar_code'],
                    'name' => $this->data['name'],
                ]);
                $sapMaterial->save();

                return $sapMaterial;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingSapMaterial($id)
    {
        try {
            $sapMaterial = SapMaterial::findOrFail($id);
            $sapMaterial->delete();
            return $sapMaterial;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
