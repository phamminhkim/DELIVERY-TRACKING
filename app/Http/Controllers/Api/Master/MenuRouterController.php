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
        $menus = $handler->getConfigMenus();

        if ($menus) {
            return $this->responseSuccess($menus);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function saveConfigMenus(Request $request)
    {
        $handler = MasterRepository::menuRouterRequest($request);
        $is_success = $handler->saveConfigMenus();

        if ($is_success) {
            return $this->responseOk();
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function deleteConfigMenu(Request $request, $id)
    {
        $handler = MasterRepository::menuRouterRequest($request);
        $is_success = $handler->deleteConfigMenu($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
