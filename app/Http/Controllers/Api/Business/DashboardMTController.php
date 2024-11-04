<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Order;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardMTController extends ResponseController
{
    public function getPoByUser(Request $request)
    {
        $handler = BusinessRepository::dashboardMTRequest($request);
        $data = $handler->getPoByUser();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getPoByCustomerGroup(Request $request)
    {
        $handler = BusinessRepository::dashboardMTRequest($request);
        $data = $handler->getPoByCustomerGroup();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getPoByDate(Request $request)
    {
        $handler = BusinessRepository::dashboardMTRequest($request);
        $data = $handler->getPoByDate();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getPOStatistics(Request $request)
    {
        $handler = BusinessRepository::dashboardMTRequest($request);
        $data = $handler->getPOStatistics();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function compareOrderReports(Request $request)
    {
        $handler = BusinessRepository::dashboardMTRequest($request);
        $data = $handler->compareOrderReports();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

}
