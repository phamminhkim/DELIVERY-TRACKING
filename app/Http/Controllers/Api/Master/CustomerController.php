<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class CustomerController extends ResponseController
{
    public function getAvailableCustomers(Request $request)
    {

        $handler = MasterRepository::customerRequest($request);
        $customers = $handler->getAvailableCustomers(false);

        if ($customers) {
            return $this->responseSuccess($customers);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getAvailableCustomersMinified(Request $request)
    {

        $handler = MasterRepository::customerRequest($request);
        $customers = $handler->getAvailableCustomers(true);

        if ($customers) {
            return $this->responseSuccess($customers);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add 
    public function createNewCustomer(Request $request)
    {

        $handler = MasterRepository::customerRequest($request);
        $customer = $handler->createNewCustomer();

        if ($customer) {
            return $this->responseSuccess($customer);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingCustomer(Request $request, $id)
    {
        $handler = MasterRepository::customerRequest($request);
        $customer = $handler->updateExistingCustomer($id);

        if ($customer) {
            return $this->responseSuccess($customer);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingCustomer(Request $request, $id)
    {
        $handler = MasterRepository::customerRequest($request);
        $is_success = $handler->deleteExistingCustomer($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
