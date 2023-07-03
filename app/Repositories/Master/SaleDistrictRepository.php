<?php

namespace App\Repositories\Master;

use App\Models\Master\SaleDistrict;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class SaleDistrictRepository extends RepositoryAbs
{
    public function getAvailableSaleDistricts()
    {
        try {
            $saleDistricts = SaleDistrict::all();
            return $saleDistricts;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewSaleDistrict()
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:sale_districts,code',
                'name' => 'required|string',
                
            ], [
                'code.required' => 'Yêu cầu nhập mã Sale District.',
                'code.string' => 'Mã Sale District phải là chuỗi.',
                'code.unique' => 'Mã Sale District đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên Sale District.',
                'name.string' => 'Tên Sale District phải là chuỗi.',
                            

            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $saleDistrict = SaleDistrict::create($this->data);

                return $saleDistrict;
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
                $saleDistrict = SaleDistrict::where('code', $value['code'])->first();
                if ($saleDistrict) {
                    $saleDistrict->update($value);
                } else {
                    SaleDistrict::create($value);
                }
            }
        }
    }
    public function updateExistingSaleDistrict($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:sale_districts,code',
                'name' => 'required|string',
                
            ], [
                'code.required' => 'Yêu cầu nhập mã kênh.',
                'code.string' => 'Mã kênh phải là chuỗi.',
                'code.unique' => 'Mã kênh đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên Sale District.',
                'name.string' => 'Tên Sale District phải là chuỗi.',
                            

            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $saleDistrict = SaleDistrict::findOrFail($id);
                $saleDistrict->update($this->data);

                return $saleDistrict;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingSaleDistrict($id)
    {
        try {
            $saleDistrict = SaleDistrict::findOrFail($id);
            $saleDistrict->delete();
            return $saleDistrict;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
