<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class CustomerMaterialController extends ResponseController
{
    public function getCustomerMaterials(Request $request)
    {

        $handler = MasterRepository::customerMaterialRequest($request);
        $customer_material = $handler->getCustomerMaterials();

        if ($customer_material) {
            return $this->responseSuccess($customer_material);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
