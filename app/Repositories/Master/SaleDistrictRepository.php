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
                $errors = $validator->errors();
                foreach ($this->data as $saleDistrict => $validator) {
                    if ($errors->has($saleDistrict)) {
                        $this->errors[$saleDistrict] = $errors->first($saleDistrict);
                        return false;
                    }
                }
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
        $result = array(
            'insert_count' => 0,
            'update_count' => 0,
            'skip_count' => 0,
            'delete_count' => 0,
            'error_count' => 0,
        );
        foreach ($this->data as $saleDistrict) {
            $validator = Validator::make($saleDistrict, [
                'code' => 'required|string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $exist_saleDistrict = SaleDistrict::where('code', $saleDistrict['code'])->first();
                if ($exist_saleDistrict) {
                    $exist_saleDistrict->update([
                        'name' => $saleDistrict['name'],
                    ]);
                    $result['update_count']++;
                } else {
                    SaleDistrict::create([
                        'code' => $saleDistrict['code'],
                        'name' => $saleDistrict['name'],
                    ]);
                    $result['insert_count']++;
                }
            }
        }
        return $result;
    }
    public function updateExistingSaleDistrict($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string',
                'name' => 'required|string',

            ], [
                'code.required' => 'Yêu cầu nhập mã kênh.',
                'code.string' => 'Mã kênh phải là chuỗi.',
                //'code.unique' => 'Mã kênh đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên Sale District.',
                'name.string' => 'Tên Sale District phải là chuỗi.',


            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $saleDistrict => $validator) {
                    if ($errors->has($saleDistrict)) {
                        $this->errors[$saleDistrict] = $errors->first($saleDistrict);
                        return false;
                    }
                }
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
