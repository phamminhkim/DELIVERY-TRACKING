<?php

namespace App\Repositories\Master;

use App\Models\Master\Customer;
use App\Models\Master\CustomerGroup;
use App\Models\Master\CustomerGroupPivot;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Abstracts\RepositoryAbs;

class CustomerGroupPivotRepository extends RepositoryAbs
{
    public function getAvailableCustomerGroupPivots()
    {
        try {
            $customerGroupPivots = CustomerGroupPivot::join('customers', 'customer_group_pivots.customer_id', '=', 'customers.id')
                ->join('customer_groups', 'customer_group_pivots.customer_group_id', '=', 'customer_groups.id')
                ->select('customer_group_pivots.*', 'customers.name as customer_name', 'customers.code', 'customer_groups.name as group_name')
                ->get();

            return $customerGroupPivots;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createNewCustomerGroupPivot()
    {
        try {
            $validator = Validator::make($this->data, [
                'customer_id' => 'required|integer|exists:customers,code',
                'customer_group_id' => 'required|integer|exists:customer_groups,id',
            ], [
                'customer_id.required' => 'Yêu cầu nhập mã khách hàng.',
                'customer_id.integer' => 'Mã khách hàng phải là chuỗi.',
                'customer_id.exists' => 'Không tìm thấy khách hàng có mã này.',
                'customer_group_id.required' => 'Yêu cầu nhập nhóm khách hàng.',
                'customer_group_id.integer' => 'Nhóm khách hàng phải là chuỗi.',
                'customer_group_id.exists' => 'Không tìm thấy nhóm khách hàng có ID này.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->toArray();
                return false;
            }

            $customer = Customer::where('code', $this->data['customer_id'])->first();

            $customerGroup = CustomerGroup::where('id', $this->data['customer_group_id'])->first();

            // Kiểm tra xem khách hàng đã tồn tại trong nhóm khách hàng hay chưa
            $existingPivot = CustomerGroupPivot::where('customer_id', $customer->id)
                ->where('customer_group_id', $customerGroup->id)
                ->first();
            if ($existingPivot) {
                $this->errors = ['customer_group_id' => 'Khách hàng đã tồn tại trong nhóm khách hàng này.'];
                return false;
            }

            $customerGroupPivotData = [
                'customer_id' => $customer->id,
                'customer_group_id' => $customerGroup->id,
            ];

            $customerGroupPivot = CustomerGroupPivot::create($customerGroupPivotData);

            return $customerGroupPivot;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateExistingCustomerGroupPivot($id)
{
    try {
        $validator = Validator::make($this->data, [
            // 'customer_id' => 'integer|exists:customers,id',
            'customer_group_id' => 'integer|exists:customer_groups,id',
        ], [
            // 'customer_id.integer' => 'Mã khách hàng phải là số nguyên.',
            // 'customer_id.exists' => 'Không tìm thấy khách hàng có mã này.',
            'customer_group_id.integer' => 'Nhóm khách hàng phải là số nguyên.',
            'customer_group_id.exists' => 'Không tìm thấy nhóm khách hàng có ID này.',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            return false;
        }

        $customerGroupPivot = CustomerGroupPivot::findOrFail($id);

        // Lưu trữ customer_id hiện tại
        $currentCustomerId = $customerGroupPivot->customer_id;
        $currentGroupId = $customerGroupPivot->customer_group_id;

        // Kiểm tra xem customer_id hoặc customer_group_id có tồn tại trong $this->data
        $updatedCustomerId = isset($this->data['customer_id'])
            ? $this->data['customer_id']
            : $currentCustomerId;

        $updatedGroupId = isset($this->data['customer_group_id'])
            ? $this->data['customer_group_id']
            : $currentGroupId;

        // Kiểm tra xem có sự thay đổi trong customer_id hoặc customer_group_id không
        if ($updatedCustomerId != $currentCustomerId) {
            $this->errors = ['customer_id' => 'Không được phép sửa khách hàng.'];
            return false;
        }

        $customerGroup = CustomerGroup::find($updatedGroupId);

        if (!$customerGroup) {
            $this->errors = ['customer_group_id' => 'Không tìm thấy nhóm khách hàng có ID này.'];
            return false;
        }

        $existingPivot = CustomerGroupPivot::where('customer_id', $updatedCustomerId)
            ->where('customer_group_id', $updatedGroupId)
            ->first();

        if ($existingPivot) {
            $this->errors = ['customer_group_id' => 'Khách hàng đã tồn tại trong nhóm khách hàng này.'];
            return false;
        }

        $customerGroupPivotData = [
            'customer_id' => $updatedCustomerId,
            'customer_group_id' => $updatedGroupId,
        ];

        $customerGroupPivot->update($customerGroupPivotData);

        return $customerGroupPivot;
    } catch (\Exception $exception) {
        $this->message = $exception->getMessage();
        $this->errors = $exception->getTrace();
        return null;
    }
}
    public function deleteExistingCustomerGroupPivot($id)
    {
        try {
            $customerGroupPivot = CustomerGroupPivot::findOrFail($id);
            $customerGroupPivot->delete();
            return $customerGroupPivot;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
