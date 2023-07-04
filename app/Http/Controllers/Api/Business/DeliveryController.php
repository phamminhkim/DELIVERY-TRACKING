<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Order;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends ResponseController
{
    public function getDeliveryByQrScan(Request $request, $qr_code)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $response = $handler->getDeliveryByQrScan($qr_code);

        if ($response) {
            return $this->responseSuccess($response);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function createDelivery(Request $request)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $response = $handler->createDelivery();

        if ($response) {
            return $this->responseSuccess($response);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function updateDelivery(Request $request, $id)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $response = $handler->updateDelivery($id);

        if ($response) {
            return $this->responseSuccess($response);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function deleteDelivery(Request $request, $id)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $is_success = $handler->deleteDelivery($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
