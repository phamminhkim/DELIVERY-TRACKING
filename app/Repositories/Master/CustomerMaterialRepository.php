<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerMaterial;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class CustomerMaterialRepository extends RepositoryAbs
{
    public function getCustomerMaterials($is_minified)
    {
        try {
            $query = CustomerMaterial::query();

            if ($this->request->filled('search')) {
                $query->search($this->request->search);
                $query->limit(50);
            }
            if ($is_minified) {
                $query->select('id', 'customer_sku_code', 'customer_sku_name', 'customer_sku_unit');
            }

            $customer_materials = $query->get();
            return $customer_materials;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
