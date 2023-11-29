<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use App\Repositories\MasterRepository;
use Illuminate\Http\Request;

class CustomerPromotionController extends ResponseController
{
    public function getAvailableCustomerPromotions(Request $request)
    {
        $handler = MasterRepository::customerPromotionRequest($request);
        $customerPromotions = $handler->getAvailableCustomerPromotions();

        if ($customerPromotions) {
            return $this->responseSuccess($customerPromotions);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function createNewCustomerPromotion(Request $request)
    {

        $handler = MasterRepository::customerPromotionRequest($request);
        $customerPromotions = $handler->createNewCustomerPromotion();

        if ($customerPromotions) {
            return $this->responseSuccess($customerPromotions);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function updateExistingCustomerPromotion(Request $request, $id)
    {

        $handler = MasterRepository::customerPromotionRequest($request);
        $customerPromotions = $handler->updateExistingCustomerPromotion($id);

        if ($customerPromotions) {
            return $this->responseSuccess($customerPromotions);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }


    public function deleteExistingCustomerPromotion(Request $request, $id)
    {
        $handler = MasterRepository::customerPromotionRequest($request);
        $is_success = $handler->deleteExistingCustomerPromotion($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
