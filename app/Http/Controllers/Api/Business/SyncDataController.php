<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;

use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;

class SyncDataController extends ResponseController
{
    public function syncSoHeaderFromSap(Request $request)
    {
        $handler = BusinessRepository::syncDataRequest($request);
        $data = $handler->syncSoHeaderFromSap();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getSoHeader(Request $request)
    {
        $handler = BusinessRepository::syncDataRequest($request);
        $data = $handler->getSoHeader();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getSoHeaderByIds(Request $request)
    {
        $handler = BusinessRepository::syncDataRequest($request);
        $data = $handler->getSoHeaderByIds();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

}
