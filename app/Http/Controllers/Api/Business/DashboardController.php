<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Order;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends ResponseController
{
    public function getStatistic(Request $request)
    {
        $handler = BusinessRepository::dashboardRequest($request);
        $data = $handler->getDashboardStatistic();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getOrdersStatistic(Request $request)
    {
        $handler = BusinessRepository::dashboardRequest($request);
        $data = $handler->getOrdersStatistic();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getCriteriaStatistic(Request $request)
    {
        $handler = BusinessRepository::dashboardRequest($request);
        $data = $handler->getCriteriaStatistic();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getReportStatistic(Request $request)
    {
        $handler = BusinessRepository::dashboardRequest($request);
        $data = $handler->getReportStatistic();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function createPublicHoliday(Request $request)
    {
        $handler = BusinessRepository::dashboardRequest($request);
        $data = $handler->createPublicHoliday();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
