<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use App\Repositories\MasterRepository;
use Illuminate\Http\Request;


class CustomerGroupPivotController extends ResponseController
{
    public function getAvailableCustomerGroupPivots(Request $request)
    {
        $handler = MasterRepository::customerGroupPivotRequest($request);
        $customerGroupPivot = $handler->getAvailableCustomerGroupPivots();

        if ($customerGroupPivot) {
            return $this->responseSuccess($customerGroupPivot);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function createNewCustomerGroupPivot(Request $request)
    {

        $handler = MasterRepository::customerGroupPivotRequest($request);
        $customerGroupPivot = $handler->createNewCustomerGroupPivot();

        if ($customerGroupPivot) {
            return $this->responseSuccess($customerGroupPivot);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function updateExistingCustomerGroupPivot(Request $request, $id)
    {

        $handler = MasterRepository::customerGroupPivotRequest($request);
        $customerGroupPivot = $handler->updateExistingCustomerGroupPivot($id);

        if ($customerGroupPivot) {
            return $this->responseSuccess($customerGroupPivot);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }


    public function deleteExistingCustomerGroupPivot(Request $request, $id)
    {
        $handler = MasterRepository::customerGroupPivotRequest($request);
        $is_success = $handler->deleteExistingCustomerGroupPivot($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
