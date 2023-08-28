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

    public function getMinifiedOrders(Request $request)
    {
        $handler = BusinessRepository::orderRequest($request);
        $orders = $handler->getOrders(true);

        if ($orders) {
            return $this->responseSuccess($orders);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getOrdersByCustomer(Request $request)
    {
        $handler = BusinessRepository::orderRequest($request);
        $orders = $handler->getOrdersByCustomer();

        if ($orders) {
            return $this->responseSuccess($orders);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getOrderById(Request $request, $order_id)
    {
        $handler = BusinessRepository::orderRequest($request);
        $order = $handler->getOrderById($order_id);

        if ($order) {
            return $this->responseSuccess($order);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getOrderExpandedById(Request $request, $order_id)
    {
        $handler = BusinessRepository::orderRequest($request);
        $order = $handler->getOrderById($order_id, true);

        if ($order) {
            return $this->responseSuccess($order);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function confirmOrder(Request $request, $order_id)
    {
        $handler = BusinessRepository::orderRequest($request);
        $order = $handler->confirmOrder($order_id);

        if ($order) {
            return $this->responseSuccess($order);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function confirmMultipleOrders(Request $request)
    {
        $handler = BusinessRepository::orderRequest($request);
        $order = $handler->confirmMultipleOrders();

        if ($order) {
            return $this->responseSuccess($order);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function reviewOrder(Request $request, $order_id)
    {
        $handler = BusinessRepository::orderRequest($request);
        $order = $handler->reviewOrder($order_id);

        if ($order) {
            return $this->responseSuccess($order);
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
