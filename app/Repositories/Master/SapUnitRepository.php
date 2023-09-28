<?php

namespace App\Repositories\Master;


use App\Models\Master\SapUnit;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;


class SapUnitRepository extends RepositoryAbs
{
    public function getAvailableSapUnits()
    {
        try {
            $sapUnits = SapUnit::all();
            return $sapUnits;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createNewSapUnit()
    {
        try {
            $validator = Validator::make($this->data, [
                'unit_code' => 'required|string|unique:sap_units,unit_code',
            ], [
                'unit_id.unique' => 'Mã unit đã tồn tại.',
                'unit_id.string' => 'Mã unit phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $sapUnit => $validator) {
                    if ($errors->has($sapUnit)) {
                        $this->errors[$sapUnit] = $errors->first($sapUnit);
                        return false;
                    }
                }
            } else{

                $sapUnit = SapUnit::create($this->data);

                return $sapUnit;

            }

        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }}

    public function updateOrInsert(){
        $result = array(
            'insert_count' => 0,
            'update_count' => 0,
            'skip_count' => 0,
            'delete_count' => 0,
            'error_count' => 0,
        );
        foreach ($this->data as $sapUnit) {
            $validator = Validator::make($sapUnit, [
                'unit_code' => 'required|string',
            ], [
                'unit_code.required' => 'Yêu cầu nhập mã unit.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $exist_sapUnit = SapUnit::where('unit_code', $sapUnit['unit_code'])->first();
                if ($exist_sapUnit) {
                    $exist_sapUnit->update([
                        'unit_name' => $sapUnit['unit_name'],
                    ]);
                    $result['update_count']++;
                } else {
                    SapUnit::create([
                        'unit_code' => $sapUnit['unit_code'],
                    ]);
                    $result['insert_count']++;
                }
            }
        }
    }
    public function updateExistingSapUnit($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'unit_code' => 'string',
            ], [
                'unit_code.string' => 'Mã Unit phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $sapUnit => $validator) {
                    if ($errors->has($sapUnit)) {
                        $this->errors[$sapUnit] = $errors->first($sapUnit);
                        return false;
                    }
                }
            } else {
                $sapUnit = SapUnit::findOrFail($id);
                $sapUnit->update($this->data);

                return $sapUnit;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingSapUnit($id)
    {
        try {
            $sapUnit = SapUnit::findOrFail($id);
            $sapUnit->delete();
            return $sapUnit;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

}
