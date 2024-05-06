<?php

namespace App\Repositories\Master;

use App\Models\Master\SapCompliance;
use App\Models\Master\SapUnit;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Company;


class SapComplianceRepository extends RepositoryAbs
{
    public function getAvailableSapCompliances($is_minified, $request)
    {
        try {
            $query = SapCompliance::query();

            if ($request->filled('search')) {
                $query->with(['unit']);
                $query->limit(50);
            }

            if ($request->filled('bar_codes')) {
                $query->whereIn('bar_code', $request->bar_codes);
            }
            if ($request->filled('sap_codes')) {
                $query->whereIn('sap_code', $request->sap_codes);
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

            if ($request->filled('search') && $request->search != null && $request->search != 'undefined') {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('sap_code', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%");
                });
            }
            if ($is_minified) {
                $query->select('id', 'name', 'sap_code', 'unit_id', 'bar_code');
            }

            $query->with(['unit' => function ($query) {
                $query->select(['id', 'unit_code']);
            }]);

            $perPage = $request->filled('per_page') ? $request->per_page : 500;
            $sap_compliances = $query->paginate($perPage, ['*'], 'page', $request->page);

            if ($request->filled('search') && $sap_compliances->isEmpty()) {
                $sap_compliances = $query->paginate($perPage, ['*'], 'page', $request->page);
            }

            $result = [
                'data' => $sap_compliances->items(),
                'per_page' => $sap_compliances->perPage(),
            ];

            $result['paginate'] = [
                'current_page' => $sap_compliances->currentPage(),
                'last_page' => $sap_compliances->lastPage(),
                'total' => $sap_compliances->total(),
            ];

            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createSapComplianceFormExcel()
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
                        $sapMaterial = SapCompliance::updateOrCreate(
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
    public function createNewSapCompliance()
    {
        try {
            $validator = Validator::make($this->data, [
                'sap_code' => 'required|string',
                'unit_id' => 'required|integer|exists:sap_units,id',
                'name' => 'required|string',
                'bar_code' => 'string',
                'quy_cach' => 'string',
                'check_qc' => 'in:0,1', // Thay đổi quy tắc kiểm tra cho check_pc

            ], [
                'sap_code.required' => 'Yêu cầu nhập mã sản phẩm.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                'sap_code.unique' => 'Mã sản phẩm đã tồn tại.',
                'unit_id.integer' => 'Mã unit phải là số nguyên.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                'name.required' => 'Yêu cầu nhập tên sản phẩm.',
                'name.string' => 'Tên sản phẩm phải là chuỗi.',
                'bar_code.string' => 'Mã Barcode phải là chuỗi.',
                'quy_cach.string' => 'Quy cách phải là chuỗi.',
                //'check_qc.integer' => 'Check quy cách phải là kiểu số nguyên.',
                'check_qc.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',

            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $SapCompliance => $validator) {
                    if ($errors->has($SapCompliance)) {
                        $this->errors[$SapCompliance] = $errors->first($SapCompliance);
                        return false;
                    }
                }
            } else {
                $sap_unit = SapUnit::find($this->data['unit_id']);
                if (!$sap_unit) {
                    $this->errors = 'Không tìm thấy mã sap_unit ' . $this->data['unit_id'];
                    return false;
                }
                $this->data['is_deleted'] = false;

                $sapComplianceData = [
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    'name' => $this->data['name'],
                ];

                if (isset($this->data['quy_cach'])) {
                    $sapComplianceData['quy_cach'] = $this->data['quy_cach'];
                }

                if (isset($this->data['bar_code'])) {
                    $sapComplianceData['bar_code'] = $this->data['bar_code'];
                }
                if (isset($this->data['check_qc'])) {
                    $sapComplianceData['check_qc'] = (int) $this->data['check_qc'];
                }

                $sapCompliance = SapCompliance::create($sapComplianceData);

                return $sapCompliance;
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
                    $exist_sap_material = SapCompliance::query()
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
                            SapCompliance::create([
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
    public function updateExistingSapCompliance($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'sap_code' => 'string:sap_materials,sap_code',
                'unit_id' => 'integer|exists:sap_units,id',
                'bar_code' => 'string',
                'name' => 'required|string',
                'quy_cach' => 'string',
                'check_qc' => 'integer|in:0,1', // Thay đổi quy tắc kiểm tra cho check_pc
            ], [
                'sap_code.required' => 'Yêu cầu nhập mã SAP.',
                'sap_code.string' => 'Mã công ty phải là chuỗi.',
                'unit_id.integer' => 'Mã unit phải là số nguyên.',
                'unit_id.exists' => 'Mã unit không tồn tại.',
                'bar_code.string' => 'Mã Barcode phải là chuỗi.',
                'name.required' => 'Yêu cầu nhập tên SAP.',
                'name.string' => 'Tên SAP phải là chuỗi.',
                'quy_cach.string' => 'Quy cách phải là chuỗi.',
                'check_qc.integer' => 'Check quy cách phải là kiểu số nguyên.',
                'check_qc.in' => 'Check quy cách chỉ được chứa giá trị 0 hoặc 1.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $SapCompliance => $validator) {
                    if ($errors->has($SapCompliance)) {
                        $this->errors[$SapCompliance] = $errors->first($SapCompliance);
                        return false;
                    }
                }
            } else {
                $sap_unit = SapUnit::find($this->data['unit_id']);
                if (!$sap_unit) {
                    $this->errors = 'Không tìm thấy mã sap_unit ' . $this->data['unit_id'];
                    return false;
                }

                $sapCompliance = SapCompliance::findOrFail($id);
                $sapCompliance->fill([
                    'sap_code' => $this->data['sap_code'],
                    'unit_id' => $this->data['unit_id'],
                    'name' => $this->data['name'],
                ]);
                if (isset($this->data['quy_cach'])) {
                    $sapCompliance->quy_cach = $this->data['quy_cach'];
                }

                if (isset($this->data['bar_code'])) {
                    $sapCompliance->bar_code = $this->data['bar_code'];
                }

                if (isset($this->data['check_qc'])) {
                    $sapCompliance->check_qc = (int) $this->data['check_qc'];
                }
                $sapCompliance->save();

                return $sapCompliance;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
        }
    }
    public function deleteExistingSapCompliance($id)
    {
        try {
            $sapCompliance = SapCompliance::findOrFail($id);
            $sapCompliance->delete();
            return $sapCompliance;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
