<?php

namespace App\Repositories;

use App\Repositories\System\OneTLRepository;
use App\Repositories\System\RouteRepository;
use App\Repositories\System\ZaloRepository;
use Illuminate\Http\Request;

class SystemRepository
{
    public static function zaloRequest(Request $request)
    {
        return new ZaloRepository($request);
    }
    public static function oneTLRequest(Request $request)
    {
        return new OneTLRepository($request);
    }
    public static function routeRequest(Request $request)
    {
        return new RouteRepository($request);
    }
}
