<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Order;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends ResponseController
{
    public function getOrders(Request $request)
    {
        $handler = BusinessRepository::orderRequest($request);
        $orders = $handler->getOrders();

        if ($orders) {
            return $this->responseSuccess($orders);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function syncFromSAP(Request $request)
    {
        $handler = BusinessRepository::orderRequest($request);
        $response = $handler->syncOrderFromSAP();

        if ($response) {
            return $this->responseSuccess($response);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
