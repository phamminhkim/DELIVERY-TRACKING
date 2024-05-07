<?php

namespace App\Repositories\Master;

use App\Models\Master\MaterialDonated;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;



class MaterialDonatedRepository extends RepositoryAbs
{
    public function getAll($is_minified)
    {
        try {
            $query = MaterialDonated::query();
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
                $query->select('id', 'sap_code', 'name');
            }
            $materialDonated = $query->orderBy('id', 'desc')->get();
            return $materialDonated;
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
                ];
                $result = collect([]);

                foreach ($data as $row) {
                    $material_donateds = MaterialDonated::updateOrCreate(

                        [
                            'sap_code' => $row[$template_structure['sap_code']],
                            'name' => $row[$template_structure['name']],
                        ]
                    );

                    $result->push($material_donateds);
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

    public function store($request)
    {
        try {
            $validator = Validator::make(
                $this->data,
                [
                    'sap_code' => 'required|unique:material_donateds,sap_code',
                    'name' => 'required',
                ],
                [
                    'sap_code.required' => 'Mã SAP không được để trống',
                    'name.required' => 'Tên không được để trống',
                ]
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $material_donated = MaterialDonated::create($this->data);
                return $material_donated;
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
                ],
                [
                    'sap_code.integer' => 'Mã SAP phải là số nguyên',
                    'name.required' => 'Tên không được để trống',
                ]
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }
            $material_donated = MaterialDonated::findOrFail($id);

            // Tiếp tục với việc cập nhật dữ liệu
            $material_donated->update($this->data);
            return $material_donated;
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
