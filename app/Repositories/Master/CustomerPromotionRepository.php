<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerPromotion;
use App\Models\Master\CustomerGroup;
use App\Models\Master\Customer;
use App\Models\Master\SapMaterial;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerPromotionRepository extends RepositoryAbs
{

    public function getAvailableCustomerPromotions()
    {
        try {
            $query = CustomerPromotion::query();

            if ($this->request->filled('search')) {
                $query->search($this->request->search);
                $query->limit(200);
            }

            if ($this->request->filled('customer_group_ids')) {
                $customer_group_ids = $this->request->customer_group_ids;
                $query->whereIn('customer_group_id', $customer_group_ids);
            }
            if ($this->request->filled('customer_ids')) {
                $customer_ids = $this->request->customer_ids;
                $query->whereIn('customer_id', $customer_ids);
            }

            if ($this->request->filled('sap_material_ids')) {
                $sap_material_ids = $this->request->sap_material_ids;
                $query->whereIn('sap_material_id', $sap_material_ids);
            }

            $query->with([
                'customer' => function ($query) {
                    $query->select(['id', 'code', 'name']);
                },
                'customer_group' => function ($query) {
                    $query->select(['id', 'name']);
                },
                'sap_material' => function ($query) {
                    $query->select(['id', 'sap_code', 'unit_id', 'name']);
                    $query->with('unit:id,unit_code');
                },
            ]);

            $customerPromotions = $query->orderBy('id', 'desc')->get();

            return $customerPromotions;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function createNewCustomerPromotion()
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($this->data, [
                'customer_group_id' => 'required|integer|exists:customer_groups,id',
                'customer_id' => 'required|integer|exists:customers,id',
                'sap_material_id' => 'required|integer|exists:sap_materials,id',
            ], [
                'customer_group_id.required' => 'Yêu cầu nhập nhóm khách hàng.',
                'customer_group_id.integer' => 'Nhóm khách hàng phải là số nguyên.',
                'customer_id.required' => 'Yêu cầu nhập mã khách hàng.',
                'customer_id.integer' => 'Mã khách hàng phải là số nguyên.',
                'sap_material_id.required' => 'Yêu cầu nhập mã đối chiếu.',
                'sap_material_id.integer' => 'Mã đối chiếu phải là số nguyên.',
                'sap_material_id.exists' => 'Mã đối chiếu không tồn tại.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }

            $customer_group_id = $this->data['customer_group_id'];
            $customer_id = $this->data['customer_id'];
            $sap_material_id = $this->data['sap_material_id'];

            // Check if the customer in the given customer group already has a sap_material_id
            $existingPromotion = CustomerPromotion::where('customer_group_id', $customer_group_id)
                ->where('customer_id', $customer_id)
                ->where('sap_material_id', $sap_material_id)
                ->first();

            if ($existingPromotion) {
                // if ($existingPromotion->customer_group_id == $customer_group_id) {
                //     $this->errors['customer_group_id'] = 'Khách hàng trong nhóm khách hàng đã có mã sản phẩm SAP này.';
                // }

                if ($existingPromotion->customer_id == $customer_id) {
                    $this->errors['customer_id'] = 'Khách hàng trong nhóm khách hàng đã có mã sản phẩm SAP này.';
                }

                return false;
            }

            $sap_material = SapMaterial::find($sap_material_id);
            if (!$sap_material) {
                $this->errors[] = 'Không tìm thấy mã đối chiếu sản phẩm ' . $sap_material_id;
                return false;
            }

            $customer_group = CustomerGroup::find($customer_group_id);
            if (!$customer_group) {
                $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $customer_group_id;
                return false;
            }

            $customer = Customer::find($customer_id);
            if (!$customer) {
                $this->errors[] = 'Không tìm thấy mã khách hàng ' . $customer_id;
                return false;
            }

            $customerPromotion = CustomerPromotion::create([
                'customer_group_id' => $customer_group_id,
                'customer_id' => $customer_id,
                'sap_material_id' => $sap_material_id,
            ]);

            DB::commit();
            return $customerPromotion;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            DB::rollBack();
            return false;
        }
    }


    public function updateExistingCustomerPromotion($id)
    {
        try {
            DB::beginTransaction();

            $customerPromotion = CustomerPromotion::find($id);
            if (!$customerPromotion) {
                $this->errors[] = 'Không tìm thấy khuyến mãi khách hàng có ID ' . $id;
                return false;
            }

            $validator = Validator::make($this->data, [
                'customer_group_id' => 'integer',
                'customer_id' => 'integer',
            ], [
                'customer_group_id.integer' => 'Nhóm khách hàng phải là số nguyên.',
                'customer_id.integer' => 'Mã khách hàng phải là số nguyên.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }

            // if (!isset($this->data['sap_material_id'])) {
            //     $this->errors[] = 'Mã đối chiếu sản phẩm không được truyền.';
            //     return false;
            // }

            $customer_group = CustomerGroup::find($this->data['customer_group_id']);
            if (!$customer_group && $this->data['customer_group_id']) {
                $this->errors[] = 'Không tìm thấy nhóm khách hàng ' . $this->data['customer_group_id'];
                return false;
            }

            $customer = Customer::find($this->data['customer_id']);
            if (!$customer && $this->data['customer_id']) {
                $this->errors[] = 'Không tìm thấy mã khách hàng ' . $this->data['customer_id'];
                return false;
            }

            // Remove sap_material_id from the update data
            // unset($this->data['sap_material_id']);
            $customerPromotion->update($this->data);

            DB::commit();
            return $customerPromotion;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            DB::rollBack();
            return false;
        }
    }
    public function deleteExistingCustomerPromotion($id)
    {
        try {
            $customerPromotions = CustomerPromotion::findOrFail($id);
            $customerPromotions->delete();
            return $customerPromotions;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
