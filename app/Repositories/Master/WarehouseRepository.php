<?php

namespace App\Repositories\Master;

use App\Models\Master\Warehouse;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class WarehouseRepository extends RepositoryAbs
{
    public function getAvailableWarehouses()
    {
        try {
            $warehouses = Warehouse::all();

            return $warehouses;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewWarehouse()
    {
        try {
            $validator = Validator::make($this->data, [
                'company_id' => 'required|integer|exists:companies,id',
                'code' => 'required|string|unique:warehouses,code',
                'name' => 'required|string',
            ], [
                'company_id.required' => 'Yêu cầu nhập ID công ty.',
                'company_id.integer' => 'ID công ty phải là số nguyên.',
                'company_id.exists' => 'Công ty được chọn không tồn tại.',
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'code.unique' => 'Mã kho đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên kho.',
                'name.string' => 'Tên kho phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $warehouse = Warehouse::create($this->request);

                return $warehouse;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateExistingWarehouse($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'company_id' => 'required|integer|exists:companies,id',
                'code' => 'required|string|unique:warehouses,code',
                'name' => 'required|string',
            ], [
                'company_id.required' => 'Yêu cầu nhập ID công ty.',
                'company_id.integer' => 'ID công ty phải là số nguyên.',
                'company_id.exists' => 'Công ty được chọn không tồn tại.',
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'code.unique' => 'Mã kho đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên kho.',
                'name.string' => 'Tên kho phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $warehouse = Warehouse::findOrFail($id);
                $warehouse->update($this->data);

                return $warehouse;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingWarehouse($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->delete();
            return $warehouse;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
