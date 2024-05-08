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
        $customer_material = $handler->getCustomerMaterials(false, $request);

        if ($customer_material) {
            return $this->responseSuccess($customer_material);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getCustomerMaterialsMinified(Request $request)
    {

        $handler = MasterRepository::customerMaterialRequest($request);
        $customer_material = $handler->getCustomerMaterials(true, $request);

        if ($customer_material) {
            return $this->responseSuccess($customer_material);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
