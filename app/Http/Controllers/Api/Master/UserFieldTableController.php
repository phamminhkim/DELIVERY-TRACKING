<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class UserFieldTableController extends ResponseController
{
    public function apiGetUserFieldTable(Request $request)
    {
        $handler = MasterRepository::userFieldTableRequest($request);
        $userFieldTable = $handler->getUserFieldTable();

        if ($userFieldTable) {
            return $this->responseSuccess($userFieldTable);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function apiGetUserFieldTableVersion_2(Request $request)
    {
        $handler = MasterRepository::userFieldTableRequest($request);
        $userFieldTable = $handler->getUserFieldTableVersion_2();

        if ($userFieldTable) {
            return $this->responseSuccess($userFieldTable);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    
    

}