<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class OrderReviewOptionController extends ResponseController
{
    public function getAvailableOrderReviewOptions(Request $request)
    {

        $handler = MasterRepository::orderReviewOptionRequest($request);
        $orderReviewOptions = $handler->getAvailableOrderReviewOptions();

        if ($orderReviewOptions) {
            return $this->responseSuccess($orderReviewOptions);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add
    public function createNewOrderReviewOption(Request $request)
    {

        $handler = MasterRepository::orderReviewOptionRequest($request);
        $orderReviewOption = $handler->createNewOrderReviewOption();

        if ($orderReviewOption) {
            return $this->responseSuccess($orderReviewOption);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingOrderReviewOption(Request $request, $id)
    {
        $handler = MasterRepository::orderReviewOptionRequest($request);
        $orderReviewOption = $handler->updateExistingOrderReviewOption($id);

        if ($orderReviewOption) {
            return $this->responseSuccess($orderReviewOption);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingOrderReviewOption(Request $request, $id)
    {
        $handler = MasterRepository::orderReviewOptionRequest($request);
        $is_success = $handler->deleteExistingOrderReviewOption($id);

        if ($is_success) {
            return $this->responseOk('success');
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
