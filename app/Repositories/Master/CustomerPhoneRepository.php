<?php

namespace App\Repositories\Master;

use App\Models\Master\Customer;
use App\Models\Master\CustomerPhone;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class CustomerPhoneRepository extends RepositoryAbs
{
    public function getAvailableCustomerPhones()
    {
        try {
            $customer_phones = CustomerPhone::all();
            return $customer_phones;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewCustomerPhone()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_id' => 'required|string|exists:customers,id',
                'phone_number' => 'required|string',
                'name' => 'required|string',
                'description' => 'required|string',
            ], [
                'customer_id.required' => 'Yêu cầu nhập ID khách hàng.',
                'customer_id.string' => 'ID khách hàng phải là chuỗi.',
                'customer_id.exists' => 'ID khách hàng không tồn tại.',
                'phone_number.required' => 'Yêu cầu nhập số điện thoại.',
                'phone_number.string' => 'Số điện thoại phải là chuỗi.',
                'name.required' => 'Yêu cầu nhập khách hàng.',
                'name.string' => 'Tên khách hàng phải là chuỗi.',
                'description.required' => 'Yêu cầu nhập mô tả.',
                'description.string' => 'Mô tả phải là chuỗi.',

            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $customer_phone => $validator) {
                    if ($errors->has($customer_phone)) {
                        $this->errors[$customer_phone] = $errors->first($customer_phone);
                        return false;
                    }
                }
            } else {
                $customer = Customer::where('id', $this->data['customer_id'])->first();
                if (!$customer) {
                    $this->errors = 'Không tìm thấy ID khách hàng ' . $this->data['customer_id'];
                    return false;
                }
                $this->data['is_active'] = true;
                $customer_phone = CustomerPhone::create([
                    'customer_id' => $customer->id,
                    'phone_number' => $this->data['phone_number'],
                    'name' => $this->data['name'],
                    'description' => $this->data['description'],
                ]);

                return $customer_phone;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function updateExistingCustomerPhone($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_id' => 'required|exists:customers,id',
                'phone_number' => 'required|string',
                'name' => 'required|string',
                'description' => 'required|string',
            ], [
                'customer_id.required' => 'Yêu cầu nhập ID khách hàng.',
                // 'customer_id.string' => 'ID khách hàng phải là chuỗi.',
                'customer_id.exists' => 'ID khách hàng không tồn tại.',
                'phone_number.required' => 'Yêu cầu nhập số điện thoại.',
                'phone_number.string' => 'Số điện thoại phải là chuỗi.',
                'name.required' => 'Yêu cầu nhập khách hàng.',
                'name.string' => 'Tên khách hàng phải là chuỗi.',
                'description.required' => 'Yêu cầu nhập mô tả.',
                'description.string' => 'Mô tả phải là chuỗi.',

            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $customer_phone => $validator) {
                    if ($errors->has($customer_phone)) {
                        $this->errors[$customer_phone] = $errors->first($customer_phone);
                        return false;
                    }
                }
            } else {
                $customer = Customer::where('id', $this->data['customer_id'])->first();
                if (!$customer) {
                    $this->errors = 'Không tìm thấy ID khách hàng ' . $this->data['customer_id'];
                    return false;
                }
                $customer_phone = CustomerPhone::findOrFail($id);
                $customer_phone->update($this->data);

                return $customer_phone;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingCustomerPhone($id)
    {
        try {
            $customer_phone = CustomerPhone::findOrFail($id);
            $customer_phone->delete();
            return $customer_phone;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
