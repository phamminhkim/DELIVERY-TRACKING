<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerMaterial;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class CustomerMaterialRepository extends RepositoryAbs
{
    public function getCustomerMaterials()
    {
        try {
            $query = CustomerMaterial::query();

            if ($this->request->filled('search')) {
                $query->search($this->request->search);
                $query->limit(50);
            }

            $customer_materials = $query->get();
            return $customer_materials;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
