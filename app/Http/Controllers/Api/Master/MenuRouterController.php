<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class MenuRouterController extends ResponseController
{
    public function getConfigMenus(Request $request)
    {
        $handler = MasterRepository::menuRouterRequest($request);
        $users = $handler->getConfigMenus();

        if ($users) {
            return $this->responseSuccess($users);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function saveConfigMenus(Request $request)
    {
        $handler = MasterRepository::menuRouterRequest($request);
        $users = $handler->saveConfigMenus();

        if ($users) {
            return $this->responseSuccess($users);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
