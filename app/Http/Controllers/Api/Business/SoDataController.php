<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;

use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;

class SoDataController extends ResponseController
{
    public function saveSoData(Request $request)
    {
        $handler = BusinessRepository::soDataRequest($request);
        $data = $handler->saveSoData();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteSoData(Request $request, $id)
    {
        $handler = BusinessRepository::soDataRequest($request);
        $data = $handler->deleteSoData($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteMultipleSo(Request $request)
    {
        $handler = BusinessRepository::soDataRequest($request);
        $data = $handler->deleteMultipleSo();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function updateSoData(Request $request, $id)
    {
        $handler = BusinessRepository::soDataRequest($request);
        $data = $handler->updateSoData($id);

        if ($data) {
            return $this->responseSuccess($data, $handler->getMessage());
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getSoData(Request $request, $id)
    {
        $handler = BusinessRepository::soDataRequest($request);
        $data = $handler->getSoData($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getOrderProcessList(Request $request)
    {
        $handler = BusinessRepository::soDataRequest($request);
        $data = $handler->getOrderProcessList();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
