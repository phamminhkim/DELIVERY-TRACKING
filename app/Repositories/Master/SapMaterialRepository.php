<?php

namespace App\Repositories\Master;

use App\Models\Master\SapMaterial;
use App\Models\Master\SapUnit;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class SapMaterialRepository extends RepositoryAbs
{
    public function getAvailableSapMaterials()
    {
        try {
            $SapMaterials = SapMaterial::all();
            return $SapMaterials;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewSapMaterial()
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:sap_materials,code',
                'name' => 'required|string',

            ], [
                'code.required' => 'Yêu cầu nhập mã material.',
                'code.string' => 'Mã material phải là chuỗi.',
                'code.unique' => 'Mã material đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên material.',
                'name.string' => 'Tên material phải là chuỗi.',
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
                $SapMaterial = SapMaterial::create($this->data);

                return $SapMaterial;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function updateOrInsert()
    {
        $validator = Validator::make($this->data, [
            '*.company_code' => 'required|string|exists:companies,code',
            '*.code' => 'required|string',
            '*.name' => 'required|string',
        ], [
            '*.company_code.required' => 'Yêu cầu nhập mã công ty.',
            '*.company_code.string' => 'Mã công ty phải là chuỗi.',
            '*.company_code.exists' => 'Mã công ty không tồn tại.',
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
                    $unit = SapUnit::where('unit_code', $material['unit_code'])->first();
                    if (!$unit) {
                        $unit = SapUnit::create(['unit_code' => $material['unit_code']]);
                    }

                    $exist_sap_material = SapMaterial::where('company_id', $material['company_code'])
                        ->where('sap_code', $material['code'])
                        ->where('unit_id', $unit->id)->first();

                    if ($material['status'] == "delete") {
                        $exist_sap_material->is_deleted = true;
                        $exist_sap_material->save();
                    } {
                        if ($exist_sap_material) {
                            $exist_sap_material->update([
                                'name' => $material['name'],
                            ]);
                            $result['update_count']++;
                        } else {
                            SapMaterial::create([
                                'company_id' => $material['company_code'],
                                'sap_code' => $material['code'],
                                'unit_id' => $unit->id,
                                'name' => $material['name'],
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
                'code' => 'required|string',
                'name' => 'required|string',

            ], [
                'code.required' => 'Yêu cầu nhập mã material.',
                'code.string' => 'Mã material phải là chuỗi.',
                //'code.unique' => 'Mã kênh đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên material.',
                'name.string' => 'Tên material phải là chuỗi.',
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
                $SapMaterial = SapMaterial::findOrFail($id);
                $SapMaterial->update($this->data);

                return $SapMaterial;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingSapMaterial($id)
    {
        try {
            $SapMaterial = SapMaterial::findOrFail($id);
            $SapMaterial->delete();
            return $SapMaterial;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
