<?php

namespace App\Repositories\Master;

use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class CustomerRepository extends RepositoryAbs
{
    public function getAvailableCustomers($is_minified)
    {
        try {
            $query = Customer::query();
            if($this->request->filled('search')){
                $query = Customer::search($this->request->search);
                $query->limit(200);   
            }
            if($is_minified){
                $query->select('id', 'name');
            }

            $customers = $query->get();
            return $customers;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getCustomerById($id) {
          try {
            $customer = Customer::find($id);
            return $customer;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewCustomer()
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:customers,code',
                'name' => 'required|string',
                'email' => 'required|string',
                'phone_number' => 'required|string',
                'address' => 'required|string',
            ], [
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
                $errors = $validator->errors();
                foreach ($this->data as $customer => $validator) {
                    if ($errors->has($customer)) {
                        $this->errors[$customer] = $errors->first($customer);
                        return false;
                    }
                }
            } else {
                $this->data['is_active'] = true;
                $customer = Customer::create($this->data);

                return $customer;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateOrInsert()
    {
        foreach ($this->data as $customer) {
            $validator = Validator::make($customer, [
                'code' => 'required|string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
            } else {
                $exist_customer = Customer::where('code', $customer['code'])->first();
                if ($exist_customer) {
                    $exist_customer->update([
                        'name' => $customer['name'],
                        'email' => $customer['email'] ?? '',
                        'phone_number' => $customer['phone_number'] ?? '',
                        'address' => $customer['address'] ?? '',
                    ]);
                } else {
                    Customer::create([
                        'code' => $customer['code'],
                        'name' => $customer['name'],
                        'email' => $customer['email'] ?? '',
                        'phone_number' => $customer['phone_number'] ?? '',
                        'address' => $customer['address'] ?? '',
                    ]);
                }
            }
        }
    }
    public function updateExistingCustomer($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string',
                'name' => 'required|string',
                'email' => 'required|string',
                'phone_number' => 'required|string',
                'address' => 'required|string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                //'code.unique' => 'Mã kho đã tồn tại.',
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
                $errors = $validator->errors();
                foreach ($this->data as $customer => $validator) {
                    if ($errors->has($customer)) {
                        $this->errors[$customer] = $errors->first($customer);
                        return false;
                    }
                }
            } else {
                $customer = Customer::findOrFail($id);
                $customer->update($this->data);

                return $customer;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingCustomer($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            return $customer;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
