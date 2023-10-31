<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\BaseController\ResponseController;
use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;

class UploadedFileController extends ResponseController
{

    public function getFiles(Request $request)
    {
        $handler = BusinessRepository::uploadedFileRequest($request);
        $data = $handler->getFiles();
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function uploadFile(Request $request)
    {
        $handler = BusinessRepository::uploadedFileRequest($request);
        $data = $handler->uploadFile();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function prepareUploadFile(Request $request)
    {
        $handler = BusinessRepository::uploadedFileRequest($request);
        $data = $handler->prepareUploadFile();

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function downloadFile(Request $request, $id)
    {
        $handler = BusinessRepository::uploadedFileRequest($request);
        $data = $handler->downloadFile($id);

        if ($data) {
            return $data;
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function deleteFile(Request $request, $id)
    {
        $handler = BusinessRepository::uploadedFileRequest($request);
        $data = $handler->deleteFile($id);

        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
