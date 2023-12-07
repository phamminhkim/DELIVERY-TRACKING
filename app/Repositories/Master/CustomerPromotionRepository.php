<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerPromotion;
use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Models\Master\SapMaterialMapping;
use App\Models\Master\SapUnit;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\Excel\ExcelExtractor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

            if ($this->request->filled('customer_material_ids')) {
                $customer_material_ids = $this->request->customer_material_ids;
                $query->whereIn('customer_material_id', $customer_material_ids);
            }

            if ($this->request->filled('sap_material_ids')) {
                $sap_material_ids = $this->request->sap_material_ids;
                $query->whereIn('sap_material_id', $sap_material_ids);
            }

            $query->with([
                'customer_material' => function ($query) {
                    $query->select(['id', 'customer_group_id', 'customer_sku_code', 'customer_sku_name', 'customer_sku_unit']);
                },
                'sap_material' => function ($query) {
                    $query->select(['id', 'sap_code', 'unit_id', 'name']);
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
                'customer_material_id' => 'required|integer|exists:customer_materials,id',
                'sap_material_id' => 'required|integer|exists:sap_materials,id',
            ], [
                'customer_material_id.required' => 'Yêu cầu nhập mã khách hàng SAP.',
                'customer_material_id.integer' => 'Mã khách hàng SAP phải là số nguyên.',
                'sap_material_id.required' => 'Yêu cầu nhập mã đối chiếu.',
                'sap_material_id.integer' => 'Mã đối chiếu phải là số nguyên.',
                'sap_material_id.exists' => 'Mã đối chiếu không tồn tại.',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors();
                return false;
            }

            $sap_material = SapMaterial::find($this->data['sap_material_id']);
            if (!$sap_material) {
                $this->errors[] = 'Không tìm thấy mã đối chiếu sản phẩm ' . $this->data['sap_material_id'];
                return false;
            }

            $customer_material = CustomerMaterial::find($this->data['customer_material_id']);
            if (!$customer_material && $this->data['customer_material_id']) {
                $this->errors[] = 'Không tìm thấy mã SKU khách hàng ' . $this->data['customer_material_id'];
                return false;
            }

            $customerPromotions = CustomerPromotion::create([
                'customer_material_id' => $this->data['customer_material_id'],
                'sap_material_id' => $this->data['sap_material_id'],
            ]);

            DB::commit();
            return $customerPromotions;
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
        $validator = Validator::make($this->data, [
            'customer_material_id' => 'integer|exists:customer_materials,id',
            'sap_material_id' => 'integer|exists:sap_materials,id',
        ], [
            'customer_material_id.integer' => 'Mã SKU khách hàng phải là số nguyên.',
            'sap_material_id.integer' => 'Mã đối chiếu phải là số nguyên.',
            'sap_material_id.exists' => 'Mã đối chiếu không tồn tại.',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            return false;
        }

        $customerPromotion = CustomerPromotion::findOrFail($id);

        // Lấy giá trị mới từ $this->data
        $updated_sap_material_id = $this->data['sap_material_id'];
        $updated_customer_material_id = $this->data['customer_material_id'];

        $customerMaterial = CustomerMaterial::find($updated_customer_material_id);
        $sapMaterial = SapMaterial::find($updated_sap_material_id);

        if (!$customerMaterial) {
            $this->errors = ['customer_material_id' => 'Không tìm thấy mã khách hàng SAP có ID này.'];
            return false;
        }

        if (!$sapMaterial) {
            $this->errors = ['sap_material_id' => 'Không tìm thấy mã đối chiếu sản phẩm này.'];
            return false;
        }

        $customerPromotion->sap_material_id = $updated_sap_material_id;
        $customerPromotion->customer_material_id = $updated_customer_material_id;
        $customerPromotion->save();

        return $customerPromotion;
    } catch (\Exception $exception) {
        $this->message = $exception->getMessage();
        $this->errors = $exception->getTrace();
        return null;
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
