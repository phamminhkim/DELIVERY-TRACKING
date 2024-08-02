<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerGroup;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class CustomerGroupRepository extends RepositoryAbs
{
    public function getAllCustomerGroups()
    {
        $query = CustomerGroup::query();

        $query
            ->with(['extract_order_configs', 'admin_extract_order_configs'])
            ->with(['customers' => function ($query) {
                $query->pluck('customer_id');
            }]);

        return $query->get();
    }
    public function createNewGroup()
    {
        try {
            $validator = Validator::make($this->data, [
                'name' => 'required|string|unique:customer_groups',
            ], [
                'name.required' => 'Yêu cầu nhập tên Group.',
                'name.string' => 'Tên Group phải là chuỗi.',
                'name.unique' => 'Tên Group đã tồn tại.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $customerGroup => $validator) {
                    if ($errors->has($customerGroup)) {
                        $this->errors[$customerGroup] = $errors->first($customerGroup);
                        return false;
                    }
                }
            } else {
                $customerGroup = CustomerGroup::create($this->data);

                return $customerGroup;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }


    public function updateExistingGroup($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'name' => 'required|string',
            ], [
                'name.required' => 'Yêu cầu nhập tên Group.',
                'name.string' => 'Tên Group phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $field => $value) {
                    if ($errors->has($field)) {
                        $this->errors[$field] = $errors->first($field);
                        return false;
                    }
                }
            } else {
                $customerGroup = CustomerGroup::findOrFail($id);
                $customerGroup->update([
                    'name' => $this->data['name'],
                    'sap_so_note_syntax' => $this->data['sap_so_note_syntax'],
                    'note' => $this->data['note'],
                ]);

                return $customerGroup;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingGroup($id)
    {
        try {
            $customerGroup = CustomerGroup::findOrFail($id);
            $customerGroup->delete();
            return $customerGroup;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
