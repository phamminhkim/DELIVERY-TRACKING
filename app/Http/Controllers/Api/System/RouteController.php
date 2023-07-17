<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\BaseController\ResponseController;
use App\Repositories\SystemRepository;
use Illuminate\Http\Request;

class RouteController extends ResponseController
{
    public function getRoutes(Request $request)
    {
        $handler = SystemRepository::routeRequest($request);
        $routes = $handler->getRoutes();

        if ($routes) {
            return $this->responseSuccess($routes);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
