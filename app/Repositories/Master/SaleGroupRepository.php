<?php

namespace App\Repositories\Master;

use App\Models\Master\SaleGroup;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class SaleGroupRepository extends RepositoryAbs
{
    public function getAvailableSaleGroups()
    {
        try {
            $saleGroups = SaleGroup::all();
            return $saleGroups;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewSaleGroup()
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:sale_groups,code',
                'name' => 'required|string',

            ], [
                'code.required' => 'Yêu cầu nhập mã Group.',
                'code.string' => 'Mã Group phải là chuỗi.',
                'code.unique' => 'Mã Group đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên Sale Group.',
                'name.string' => 'Tên Sale Group phải là chuỗi.',


            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $saleGroup => $validator) {
                    if ($errors->has($saleGroup)) {
                        $this->errors[$saleGroup] = $errors->first($saleGroup);
                        return false;
                    }
                }
            } else {
                $saleGroup = SaleGroup::create($this->data);

                return $saleGroup;
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
        foreach ($this->data as $saleGroup) {
            $validator = Validator::make($saleGroup, [
                'code' => 'required|string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $exist_sale_group = SaleGroup::where('code', $saleGroup['code'])->first();
                if ($exist_sale_group) {
                    $exist_sale_group->update([
                        'name' => $saleGroup['name'],
                    ]);
                    $result['update_count']++;
                } else {
                    SaleGroup::create([
                        'code' => $saleGroup['code'],
                        'name' => $saleGroup['name'],
                    ]);
                    $result['update_count']++;
                }
            }
        }
        return $result;
    }
    public function updateExistingSaleGroup($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string',
                'name' => 'required|string',

            ], [
                'code.required' => 'Yêu cầu nhập mã Group.',
                'code.string' => 'Mã Group phải là chuỗi.',
                //'code.unique' => 'Mã Group đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên Sale Group.',
                'name.string' => 'Tên Sale Group phải là chuỗi.',


            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $saleGroup => $validator) {
                    if ($errors->has($saleGroup)) {
                        $this->errors[$saleGroup] = $errors->first($saleGroup);
                        return false;
                    }
                }
            } else {
                $saleGroup = SaleGroup::findOrFail($id);
                $saleGroup->update($this->data);

                return $saleGroup;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingSaleGroup($id)
    {
        try {
            $saleGroup = SaleGroup::findOrFail($id);
            $saleGroup->delete();
            return $saleGroup;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
