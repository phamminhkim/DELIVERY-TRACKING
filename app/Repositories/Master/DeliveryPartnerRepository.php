<?php

namespace App\Repositories\Master;

use App\Models\Master\DeliveryPartner;
use App\Models\Master\DistributionChannel;
use App\Models\Master\UserMorph;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DeliveryPartnerRepository extends RepositoryAbs
{
    public function getAvailablePartners()
    {
        try {
            if (!$this->current_user->hasRole(['admin-system', 'admin-warehouse', 'admin-partner'])) {
                return collect([]);
            }
            $query = DeliveryPartner::query();
            if ($this->current_user->hasRole('admin-partner')) {
                $partner_ids = $this->current_user->delivery_partners->pluck('id')->toArray();
                $query->whereIn('id', $partner_ids);
            }
            $partners = $query->with(['users', 'distribution_channels'])->withCount('users')->get()->map(function ($partner) {
                $partner->channel_ids = $partner->distribution_channels->pluck('id')->toArray();
                $partner->user_ids = $partner->users->pluck('user_id')->toArray();
                return $partner;
            });
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
                'api_body_datas' => 'nullable',
                'api_delivery_code_field' => 'string|nullable',
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
                    'api_body_datas' => $this->data['api_body_datas'] ?? [], // Sử dụng giá trị mặc định là mảng rỗng
                    'api_delivery_code_field' => $this->data['api_delivery_code_field'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                ]);
                foreach ($this->data['user_ids'] as $userId) {
                    $userMorph = new UserMorph(['user_id' => $userId]);
                    $partner->users()->save($userMorph);
                }
                $partner->distribution_channels()->sync($this->data['channel_ids']);
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
                'api_body_datas' => 'nullable',
                'api_delivery_code_field' => 'string|nullable',
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
                $partner->fill([
                    'code' => $this->data['code'],
                    'name' => $this->data['name'],
                    'is_external' => true,
                    'is_active' => true,
                    'api_url' => $this->data['api_url'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                    'api_key' => $this->data['api_key'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                    'api_secret' => $this->data['api_secret'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                    'api_body_datas' => $this->data['api_body_datas'] ? $this->data['api_body_datas'] : [], // Sử dụng giá trị mặc định là mảng rỗng
                    'api_delivery_code_field' => $this->data['api_delivery_code_field'] ?? '', // Sử dụng giá trị mặc định là chuỗi rỗng
                ]);
                // Detach all existing relationships
                $partner->users()->delete();

                // Attach new relationships
                foreach ($this->data['user_ids'] as $userId) {
                    $userMorph = new UserMorph(['user_id' => $userId]);
                    $partner->users()->save($userMorph);
                }
                $partner->distribution_channels()->sync($this->data['channel_ids']);
                $partner->save();
                $partner->load(['users', 'distribution_channels']);
                $partner->users_count = $partner->users()->count();
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
