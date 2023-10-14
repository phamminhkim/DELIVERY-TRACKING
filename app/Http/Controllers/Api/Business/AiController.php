<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Models\Business\Order;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AiController extends ResponseController
{
    public function extractOrder(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->extractOrder();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function extractDataForConfig(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->extractDataForConfig();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function convertToTableForConfig(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->convertToTableForConfig();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function restructureDataForConfig(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->restructureDataForConfig();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getExtractOrderConfigs(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->getExtractOrderConfigs();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function createExtractOrderConfigs(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->createExtractOrderConfigs();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function updateExtractOrderConfig(Request $request, $id)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->updateExtractOrderConfig($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }


    public function uploadFile(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->uploadFile();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function prepareUploadFile(Request $request)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->prepareUploadFile();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function downloadFile(Request $request, $id)
    {
        $handler = BusinessRepository::aiRequest($request);
        $data = $handler->getFile($id);

        if ($data) {
            return $data;
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
