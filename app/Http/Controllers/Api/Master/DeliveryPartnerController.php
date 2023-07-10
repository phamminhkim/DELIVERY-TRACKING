<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class DeliveryPartnerController extends ResponseController
{
    public function getAvailablePartners(Request $request)
    {

        $handler = MasterRepository::deliveryPartnerRequest($request);
        $partners = $handler->getAvailablePartners();

        if ($partners) {
            return $this->responseSuccess($partners);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add
    public function createNewPartner(Request $request)
    {

        $handler = MasterRepository::deliveryPartnerRequest($request);
        $partner = $handler->createNewPartner();

        if ($partner) {
            return $this->responseSuccess($partner);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingPartner(Request $request, $id)
    {
        $handler = MasterRepository::deliveryPartnerRequest($request);
        $partner = $handler->updateExistingPartner($id);

        if ($partner) {
            return $this->responseSuccess($partner);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingPartner(Request $request, $id)
    {
        $handler = MasterRepository::deliveryPartnerRequest($request);
        $is_success = $handler->deleteExistingPartner($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
