<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;

class CheckDataController extends ResponseController
{
    public function checkMaterialSAP(Request $request, $id)
    {
        $handler = BusinessRepository::checkDataRequest($request);
        $data = $handler->checkMaterialSAP($id);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    // public function checkInventory(Request $request,$id)
    // {
    //     $handler = BusinessRepository::checkDataRequest($request);
    //     $data = $handler->checkInventory($id);
    //     if ($data) {
    //         return $this->responseSuccess($data);
    //     } else {
    //         return $this->responseError($handler->getMessage(), $handler->getErrors());
    //     }
    // }
}
