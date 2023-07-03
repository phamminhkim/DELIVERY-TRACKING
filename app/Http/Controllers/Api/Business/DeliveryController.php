<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Order;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends ResponseController
{
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
}
