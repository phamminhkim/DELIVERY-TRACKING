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
                'api_url' => 'required|string',
                'api_key' => 'required|string',
                'api_secret' => 'required|string',
                //'is_external' => 'required',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                'code.unique' => 'Mã kho đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên nhà vận chuyển.',
                'name.string' => 'Tên nhà vận chuyển phải là chuỗi.',
                'api_url.required' => 'Yêu cầu nhập api_url.',
                'api_url.string' => 'Api Url phải là chuỗi.',
                'api_key.required' => 'Yêu cầu nhập api_key.',
                'api_key.string' => 'Api key phải là chuỗi.',
                'api_secret.required' => 'Yêu cầu nhập api_secret.',
                'api_secret.string' => 'Api secret phải là chuỗi.',

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
                $partner = DeliveryPartner::create($this->data);

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
                'api_url' => 'required|string',
                'api_key' => 'required|string',
                'api_secret' => 'required|string',
                //'is_external' => 'required',
            ], [
                'code.required' => 'Yêu cầu nhập mã kho.',
                'code.string' => 'Mã kho phải là chuỗi.',
                //'code.unique' => 'Mã kho đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên nhà vận chuyển.',
                'name.string' => 'Tên nhà vận chuyển phải là chuỗi.',
                'api_url.required' => 'Yêu cầu nhập api_url.',
                'api_url.string' => 'Api Url phải là chuỗi.',
                'api_key.required' => 'Yêu cầu nhập api_key.',
                'api_key.string' => 'Api key phải là chuỗi.',
                'api_secret.required' => 'Yêu cầu nhập api_secret.',
                'api_secret.string' => 'Api secret phải là chuỗi.',
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
                $partner->update($this->data);

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
