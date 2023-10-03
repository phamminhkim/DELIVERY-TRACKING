<?php

namespace App\Repositories\Master;

use App\Models\Master\Warehouse;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\Company;

class WarehouseRepository extends RepositoryAbs
{
    public function getAvailableWarehouses($is_minified, $is_active)
    {
        try {
            $query = Warehouse::query();
            if($is_minified){
                $query->select('id', 'name');
            }
            if($is_active){
                $query->where('is_active', true);
            }
            $warehouses = $query->get();
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
                'company_code' => 'required|integer|exists:companies,code',
                'code' => 'required|string|unique:warehouses,code',
                'name' => 'required|string',
            ], [
                'company_code.required' => 'Yêu cầu nhập ID công ty.',
                'company_code.integer' => 'ID công ty phải là số nguyên.',
                'company_code.exists' => 'Công ty được chọn không tồn tại.',
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'code.unique' => 'Mã kho đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên kho.',
                'name.string' => 'Tên kho phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $warehouse => $validator) {
                    if ($errors->has($warehouse)) {
                        $this->errors[$warehouse] = $errors->first($warehouse);
                        return false;
                    }
                }
            } else {
                $this->data['is_active'] = true;
                $company = Company::where('code', $this->data['company_code'])->first();
                if (!$company) {
                    $this->errors = 'Không tìm thấy công ty có mã ' . $this->data['company_code'];
                    return false;
                }
                $warehouse = Warehouse::create([
                    'company_code' => $company->code,
                    'code' => $this->data['code'],
                    'name' => $this->data['name'],
                ]);
                return $warehouse;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
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
        foreach ($this->data as $warehouse) {
            // dd("test2");
            $validator = Validator::make($warehouse, [
                'company_code' => 'required|integer|exists:companies,code',
                'code' => 'required|string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                //dd("abc");
                $company = Company::where('code', $warehouse['company_code'])->first();
                // dd($company);
                if (!$company) {
                    $result['error_count']++;
                    $this->errors[] = 'Không tìm thấy công ty có mã ' . $warehouse['company_code'];
                    continue;
                }
                $exist_warehouse = Warehouse::where('code', $warehouse['code'])->first();
                if ($exist_warehouse) {
                    $exist_warehouse->update([

                        'company_code' => $company->code,
                        'name' => $warehouse['name'],
                    ]);
                    $result['update_count']++;
                } else {
                    //dd('test1');
                    Warehouse::create([
                        'company_code' => $company->code,
                        'code' => $warehouse['code'],
                        'name' => $warehouse['name'],
                    ]);
                    $result['insert_count']++;
                }
            }
        }
    }
    public function updateExistingWarehouse($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'company_code' => 'required|integer|exists:companies,code',
                'code' => 'required|string',
                'name' => 'required|string',
            ], [
                'company_code.required' => 'Yêu cầu nhập ID công ty.',
                'company_code.integer' => 'ID công ty phải là số nguyên.',
                'company_code.exists' => 'Công ty được chọn không tồn tại.',
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                //'code.unique' => 'Mã kho đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên kho.',
                'name.string' => 'Tên kho phải là chuỗi.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $warehouse => $validator) {
                    if ($errors->has($warehouse)) {
                        $this->errors[$warehouse] = $errors->first($warehouse);
                        return false;
                    }
                }

            } else {
                $company = Company::where('code', $this->data['company_code'])->first();
                if (!$company) {
                    $this->errors = 'Không tìm thấy công ty có mã ' . $this->data['company_code'];
                    return false;
                }
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
