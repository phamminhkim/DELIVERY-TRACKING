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
            $query = CustomerPhone::query();
            $query->leftJoin('customers', 'customers.id', '=', 'customer_phones.customer_id')
                ->select('customer_phones.*', 'customers.name as customer_name');

            $customer_phones = $query->get();
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
                'customer_id' => 'required|exists:customers,id',
                'phone_number' => 'required|string',
                'name' => 'required|string',
                'description' => 'required|string',
                'is_active' => 'boolean',
                'is_receive_sms' => 'boolean',
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
                // 'is_active.required' => 'Yêu cầu nhập trạng thái.',
                'is_active.boolean' => 'Trạng thái phải là boolean.',
                // 'is_receive_message.required' => 'Yêu cầu nhập trạng thái nhận tin nhắn.',
                'is_receive_sms.boolean' => 'Trạng thái nhận tin nhắn phải là boolean.', 
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
                    'is_active' => $this->data['is_active'],
                    'is_receive_sms' => $this->data['is_receive_sms'],
                ]);
                $customer = Customer::find($this->data['customer_id']);
                $customer_phone['customer_name'] = $customer->name;
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
                'is_active' => 'boolean',
                'is_receive_sms' => 'boolean',
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
                $customer = Customer::find($customer_phone->customer_id);
                $customer_phone['customer_name'] = $customer->name;
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
