<?php

namespace App\Repositories;

use App\Repositories\Business\OrderRepository;
use App\Repositories\Business\DeliveryRepository;
use Illuminate\Http\Request;

class BusinessRepository
{
    public static function orderRequest(Request $request)
    {
        return new OrderRepository($request);
    }
    public static function deliveryRequest(Request $request)
    {
        return new DeliveryRepository($request);
    }
}
