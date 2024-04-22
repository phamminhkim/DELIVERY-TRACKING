<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class CustomerPartnerController extends ResponseController
{
    public function getAvailableCustomerPartners(Request $request)
    {
        $handler = MasterRepository::CustomerPartnerRequest($request);
        $customerPartners = $handler->getAvailableCustomerPartners(false);

        if ($customerPartners) {
            return $this->responseSuccess($customerPartners);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getAvailableCustomerPartnersMinified(Request $request)
    {

        $handler = MasterRepository::CustomerPartnerRequest($request);
        $customerPartners = $handler->getAvailableCustomerPartners(true);

        if ($customerPartners) {
            return $this->responseSuccess($customerPartners);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add
    public function createNewCustomerPartner(Request $request)
    {

        $handler = MasterRepository::CustomerPartnerRequest($request);
        $customerPartner = $handler->createNewCustomerPartner();

        if ($customerPartner) {
            return $this->responseSuccess($customerPartner);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingCustomerPartner(Request $request, $id)
    {
        $handler = MasterRepository::CustomerPartnerRequest($request);
        $customer_partner = $handler->updateExistingCustomerPartner($id);

        if ($customer_partner) {
            return $this->responseSuccess($customer_partner);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingCustomerPartner(Request $request, $id)
    {
        $handler = MasterRepository::CustomerPartnerRequest($request);
        $is_success = $handler->deleteExistingCustomerPartner($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
