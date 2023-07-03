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
                $this->errors = $validator->errors()->all();
            } else {
                $saleGroup = SaleGroup::create($this->data);

                return $saleGroup;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function updateOrInsert(){
        foreach ($this->data as $value) {
            $validator = Validator::make($value, [
                'code' => 'required|string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
            ]);
    
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $saleGroup = SaleGroup::where('code', $value['code'])->first();
                if ($saleGroup) {
                    $saleGroup->update($value);
                } else {
                    SaleGroup::create($value);
                }
            }
        }
    }
    public function updateExistingSaleGroup($id)
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
                $this->errors = $validator->errors()->all();
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
