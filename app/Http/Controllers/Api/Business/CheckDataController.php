<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;

class CheckDataController extends ResponseController
{
    public function checkMaterialSAP(Request $request)
    {
        $handler = BusinessRepository::checkDataRequest($request);
        $data = $handler->checkMaterialSAP($request);
        if ($data) {
            return $this->response($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function checkPromotions(Request $request)
    {
        $handler = BusinessRepository::checkDataRequest($request);
        $data = $handler->checkPromotions($request);
        if ($data) {
            return $this->response($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function checkInventory(Request $request)
    {
        $handler = BusinessRepository::checkDataRequest($request);
        $data = $handler->checkInventory();
        if ($data) {
            return $this->response($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function checkPrice(Request $request)
    {
        $handler = BusinessRepository::checkDataRequest($request);
        $data = $handler->checkPrice();
        if ($data) {
            return $this->response($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
