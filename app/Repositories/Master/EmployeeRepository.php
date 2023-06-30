<?php

namespace App\Repositories\Master;

use App\Models\Master\Employee;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class EmployeeRepository extends RepositoryAbs
{
    public function getAvailableEmployees()
    {
        try {
            $employees = Employee::all();
            return $employees;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewEmployee()
    {
        try {
            $validator = Validator::make($this->data, [
                'user_id' =>'required|integer|exists:users,id',
                'company_code' => 'required|string',                
                'code' => 'required|string|unique:employees,code',
                'name' => 'required|string',
                'email' => 'required|string',
                'phone_number' => 'required|string',
                'address' => 'required|string',
            ], [
                'user_id.required' => 'Yêu cầu nhập ID User.',
                'user_id.integer' => 'ID User phải là số nguyên.',
                'company_code.required' => 'Yêu cầu nhập mã công ty.',
                'company_code.string' => 'Mã công ty phải là chuỗi.',
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'code.unique' => 'Mã kho đã tồn tại.',
                'name.required' => 'Yêu cầu nhập khách hàng.',
                'name.string' => 'Tên khách hàng phải là chuỗi.',
                'email.required' => 'Yêu cầu nhập email.',
                'email.string' => 'Email phải là chuỗi.',
                'phone_number.required' => 'Yêu cầu nhập số điện thoại.',
                'phone_number.string' => 'Số điện thoại phải là chuỗi.',
                'address.required' => 'Yêu cầu nhập địa chỉ.',
                'address.string' => 'Địa chỉ phải là chuỗi.',                

            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $employee = Employee::create($this->data);

                return $employee;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateExistingEmployee($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'user_id' =>'required|integer|exists:users,id',
                'company_code' => 'required|string',                
                'code' => 'required|string|unique:employees,code',
                'name' => 'required|string',
                'email' => 'required|string',
                'phone_number' => 'required|string',
                'address' => 'required|string',
            ], [
                'user_id.required' => 'Yêu cầu nhập ID User.',
                'user_id.integer' => 'ID User phải là số nguyên.',
                'company_code.required' => 'Yêu cầu nhập mã công ty.',
                'company_code.string' => 'Mã công ty phải là chuỗi.',
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'code.unique' => 'Mã kho đã tồn tại.',
                'name.required' => 'Yêu cầu nhập khách hàng.',
                'name.string' => 'Tên khách hàng phải là chuỗi.',
                'email.required' => 'Yêu cầu nhập email.',
                'email.string' => 'Email phải là chuỗi.',
                'phone_number.required' => 'Yêu cầu nhập số điện thoại.',
                'phone_number.string' => 'Số điện thoại phải là chuỗi.',
                'address.required' => 'Yêu cầu nhập địa chỉ.',
                'address.string' => 'Địa chỉ phải là chuỗi.',                

            ]);
            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $employee = Employee::findOrFail($id);
                $employee->update($this->data);

                return $employee;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingEmployee($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
            return $employee;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
