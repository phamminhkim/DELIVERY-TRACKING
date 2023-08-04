<?php

namespace App\Repositories\Master;

use App\Models\Master\DeliveryPartner;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DeliveryPartnerRepository extends RepositoryAbs
{
    public function getAvailablePartners()
    {
        try {
            // $partners = DeliveryPartner::all();
            $partners = DB::table('delivery_partners')->get();
            return $partners;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewPartner()
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:delivery_partners,code',
                'name' => 'required|string',
                'api_url' => 'string|nullable',
                'api_key' => 'string|nullable',
                'api_secret' => 'string|nullable',
                //'is_external' => 'required',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'code.unique' => 'Mã kho đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên nhà vận chuyển.',
                'name.string' => 'Tên nhà vận chuyển phải là chuỗi.',
                'api_url.string' => 'Api Url phải là chuỗi.',
                'api_key.string' => 'Api Key phải là chuỗi.',
                'api_secret.string' => 'Api Secret phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $partner => $validator) {
                    if ($errors->has($partner)) {
                        $this->errors[$partner] = $errors->first($partner);
                        return false;
                    }
                }
            } else {
                $this->data['is_external'] = true;
                $this->data['is_active'] = true;
                $partner = DeliveryPartner::create([
                    'code' => $this->data['code'],
                    'name' => $this->data['name'],
                    'is_external' => true,
                    'is_active' => true,
                    'api_url' => $this->data['api_url'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                    'api_key' => $this->data['api_key'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                    'api_secret' => $this->data['api_secret'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                ]);

                return $partner;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateExistingPartner($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string',
                'name' => 'required|string',
                'api_url' => 'string|nullable',
                'api_key' => 'string|nullable',
                'api_secret' => 'string|nullable',
                //'is_external' => 'required',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'name.required' => 'Yêu cầu nhập tên nhà vận chuyển.',
                'name.string' => 'Tên nhà vận chuyển phải là chuỗi.',
                'api_url.string' => 'Api Url phải là chuỗi.',
                'api_key.string' => 'Api Key phải là chuỗi.',
                'api_secret.string' => 'Api Secret phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $partner => $validator) {
                    if ($errors->has($partner)) {
                        $this->errors[$partner] = $errors->first($partner);
                        return false;
                    }
                }
            } else {
                $partner = DeliveryPartner::findOrFail($id);
                $partner = DeliveryPartner::create([
                    'code' => $this->data['code'],
                    'name' => $this->data['name'],
                    'is_external' => true,
                    'is_active' => true,
                    'api_url' => $this->data['api_url'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                    'api_key' => $this->data['api_key'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                    'api_secret' => $this->data['api_secret'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                ]);

                return $partner;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingPartner($id)
    {
        try {
            $partner = DeliveryPartner::findOrFail($id);
            $partner->delete();
            return $partner;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
