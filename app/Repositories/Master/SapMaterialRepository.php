<?php

namespace App\Repositories\Master;

use App\Models\Master\SapMaterial;
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
        $result = array(
            'insert_count' => 0,
            'update_count' => 0,
            'skip_count' => 0,
            'delete_count' => 0,
            'error_count' => 0,
        );
        foreach ($this->data as $SapMaterial) {
            $validator = Validator::make($SapMaterial, [
                'code' => 'required|string',
            ], [
                'code.required' => 'Yêu cầu nhập mã material.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $exist_SapMaterial = SapMaterial::where('code', $SapMaterial['code'])->first();
                if ($exist_SapMaterial) {
                    $exist_SapMaterial->update([
                        'name' => $SapMaterial['name'],
                    ]);
                    $result['update_count']++;
                } else {
                    SapMaterial::create([
                        'code' => $SapMaterial['code'],
                        'name' => $SapMaterial['name'],
                    ]);
                    $result['insert_count']++;
                }
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
