<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use App\Http\Controllers\Controller;
use App\Repositories\MasterRepository;
use Illuminate\Http\Request;

class CustomerGroupController extends ResponseController
{
    public function getAllCustomerGroups(Request $request)
    {
        $handler = MasterRepository::customerGroupRequest($request);
        $customer_groups = $handler->getAllCustomerGroups();

        if ($customer_groups) {
            return $this->responseSuccess($customer_groups);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
