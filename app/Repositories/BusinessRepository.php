<?php

namespace App\Repositories;

use App\Repositories\Business\OrderRepository;
use Illuminate\Http\Request;

class BusinessRepository
{
    public static function orderRequest(Request $request)
    {
        return new OrderRepository($request);
    }
}
