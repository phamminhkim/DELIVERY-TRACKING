<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerPartner;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class CustomerPartnerRepository extends RepositoryAbs
{
    public function getAvailableCustomerPartners($is_minified)
    {
        try {

            $query = CustomerPartner::query();
            if ($this->request->filled('search')) {
                $query = $query->search($this->request->search);
                $query->limit(200);
            }
            if ($this->request->filled('ids')) {
                $query->whereIn('id', $this->request->ids);
            }
            if ($is_minified) {
                $query->select('id', 'code', 'name',);
            }

            $customer_partners = $query->get();

            return $customer_partners;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getCustomerPartnerById($id)
    {
        try {
        $customer_partners = CustomerPartner::find($id);
            return $customer_partners;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewCustomerPartner()
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required:customer_partners,code',
                'name' => 'string',
                'note' => 'string',
                'LV2' => 'string',
                'LV3' => 'string',
                'LV4' => 'string',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'name.string' => 'Tên khách hàng phải là chuỗi.',
                'note.string' => 'Ghi chú phải là chuỗi.',
                'LV2.string' => 'LV2 phải là chuỗi.',
                'LV3.string' => 'LV3 phải là chuỗi.',
                'LV4.string' => 'LV4 phải là chuỗi.',

            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $customer_partner => $validator) {
                    if ($errors->has($customer_partner)) {
                        $this->errors[$customer_partner] = $errors->first($customer_partner);
                        return false;
                    }
                }
            } else {
                $this->data['is_deleted'] = false;
                $customer_partner = CustomerPartner::create($this->data);

                return $customer_partner;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    // public function updateOrInsert()
    // {
    //     foreach ($this->data as $customer_partner) {
    //         $validator = Validator::make($customer_partner, [
    //             'code' => 'required|string',
    //         ], [
    //             'code.required' => 'Yêu cầu nhập mã kho.',
    //         ]);

    //         if ($validator->fails()) {
    //             $this->errors = $validator->errors()->all();
    //         } else {
    //             $exist_customer_partner = CustomerPartner::where('code', $customer_partner['code'])->first();
    //             if ($exist_customer_partner) {
    //                 $exist_customer_partner->update([
    //                     'name' => $customer_partner['name'],
    //                     'code' => $customer_partner['code'] ?? '',
    //                     'note' => $customer_partner['note'] ?? '',
    //                 ]);
    //             } else {
    //                 CustomerPartner::create([
    //                     'code' => $customer_partner['code'],
    //                     'name' => $customer_partner['name'],
    //                     'note' => $customer_partner['note'] ?? '',
    //                 ]);
    //             }
    //         }
    //     }
    // }
    public function updateExistingCustomerPartner($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'string',
                'name' => 'string',
                'note' => 'string',
                'LV2' => 'string',
                'LV3' => 'string',
                'LV4' => 'string',
            ], [
                'code.string' => 'Mã kho phải là chuỗi.',
                'name.string' => 'Tên khách hàng phải là chuỗi.',
                'note.string' => 'Ghi chú phải là chuỗi.',
                'LV2.string' => 'LV2 phải là chuỗi.',
                'LV3.string' => 'LV3 phải là chuỗi.',
                'LV4.string' => 'LV4 phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $customer_partner => $validator) {
                    if ($errors->has($customer_partner)) {
                        $this->errors[$customer_partner] = $errors->first($customer_partner);
                        return false;
                    }
                }
            } else {
                $customer_partner = CustomerPartner::findOrFail($id);
                $customer_partner->update($this->data);

                return $customer_partner;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingCustomerPartner($id)
    {
        try {
            $customer_partner = CustomerPartner::findOrFail($id);
            $customer_partner->delete();
            return $customer_partner;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
