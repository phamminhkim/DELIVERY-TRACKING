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
        $response = $handler->getDeliveryByQrScan($qr_code, false);

        if ($response) {
            return $this->responseSuccess($response);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getCustomerDeliveryByQrScan(Request $request, $qr_code)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $response = $handler->getDeliveryByQrScan($qr_code, true);

        if ($response) {
            return $this->responseSuccess($response);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getDeliveryById(Request $request, $delivery_id)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $response = $handler->getDeliveryById($delivery_id);

        if ($response) {
            return $this->responseSuccess($response);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getDeliveries(Request $request)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $response = $handler->getDeliveries();

        if ($response) {
            return $this->responseSuccess($response);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function printDeliveryQrCodeById(Request $request, $delivery_id)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $response = $handler->printDeliveryQrCodeById($delivery_id);

        if ($response) {
            return $this->responseSuccess($response);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function confirmPickupDelivery(Request $request, $delivery_id)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $is_success = $handler->confirmPickupDelivery($delivery_id);

        if ($is_success) {
            return $this->responseSuccess($is_success);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function completeDelivery(Request $request, $delivery_id)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $is_success = $handler->completeDelivery($delivery_id);

        if ($is_success) {
            return $this->responseSuccess($is_success);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function confirmOrderDelivery(Request $request, $delivery_id, $order_id)
    {
        $handler = BusinessRepository::deliveryRequest($request);
        $is_success = $handler->confirmOrderDelivery($delivery_id, $order_id);

        if ($is_success) {
            return $this->responseSuccess($is_success);
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
