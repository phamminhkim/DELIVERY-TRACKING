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
    //add
    public function createNewGroup(Request $request)
    {

        $handler = MasterRepository::customerGroupRequest($request);
        $customerGroup = $handler->createNewGroup();

        if ($customerGroup) {
            return $this->responseSuccess($customerGroup);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingGroup(Request $request, $id)
    {
        $handler = MasterRepository::customerGroupRequest($request);
        $customerGroup = $handler->updateExistingGroup($id);

        if ($customerGroup) {
            return $this->responseSuccess($customerGroup);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingGroup(Request $request, $id)
    {
        $handler = MasterRepository::customerGroupRequest($request);
        $is_success = $handler->deleteExistingGroup($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
