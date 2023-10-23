<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerMaterial;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class CustomerMaterialRepository extends RepositoryAbs
{
    public function getAllCustomerMaterials()
    {
        try {
            $customerMaterials = CustomerMaterial::all();
            return $customerMaterials;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }

    }
}
