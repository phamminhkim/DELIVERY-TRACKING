<?php

namespace App\Repositories\Master;

use App\Models\Master\MaterialDonated;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class MaterialDonatedRepository extends RepositoryAbs
{
    public function getAll($request)
    {
        try {
            $query = MaterialDonated::query();
            if($request->filled('sap_codes')){
                $query = $query->whereIn('sap_code',$request->sap_codes);
            }   
            $materialDonated = $query->orderBy('id', 'desc')->get();
            return $materialDonated;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function store($request)
    {
        try {
            $validator = Validator::make($this->data,
                [
                    'sap_code' => 'required|unique:material_donateds,sap_code',
                    'name' => 'required',
                ],
                [
                    'sap_code.required' => 'Mã SAP không được để trống',
                    'sap_code.unique' => 'Mã SAP đã tồn tại',
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
            $validator = Validator::make($this->data,
                [
                    'sap_code' => 'required|unique:material_donateds,sap_code,' . $id,
                    'name' => 'required',
                ],
                [
                    'sap_code.required' => 'Mã SAP không được để trống',
                    'sap_code.unique' => 'Mã SAP đã tồn tại',
                    'name.required' => 'Tên không được để trống',
                ]
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors();
            } else {
                $material_donated = MaterialDonated::find($id);
                $material_donated->update($this->data);
                return $material_donated;
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
