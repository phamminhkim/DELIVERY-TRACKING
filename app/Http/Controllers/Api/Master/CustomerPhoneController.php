<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class CustomerPhoneController extends ResponseController
{
    public function getAvailableCustomerPhones(Request $request)
    {

        $handler = MasterRepository::customerPhoneRequest($request);
        $customer_phones = $handler->getAvailableCustomerPhones();

        if ($customer_phones) {
            return $this->responseSuccess($customer_phones);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add
    public function createNewCustomerPhone(Request $request)
    {

        $handler = MasterRepository::customerPhoneRequest($request);
        $customer_phone = $handler->createNewCustomerPhone();

        if ($customer_phone) {
            return $this->responseSuccess($customer_phone);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingCustomerPhone(Request $request, $id)
    {
        $handler = MasterRepository::customerPhoneRequest($request);
        $customer_phone = $handler->updateExistingCustomerPhone($id);

        if ($customer_phone) {
            return $this->responseSuccess($customer_phone);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingCustomerPhone(Request $request, $id)
    {
        $handler = MasterRepository::customerPhoneRequest($request);
        $is_success = $handler->deleteExistingCustomerPhone($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
