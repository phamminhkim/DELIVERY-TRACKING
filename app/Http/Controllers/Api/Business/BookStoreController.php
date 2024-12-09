<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\BaseController\ResponseController;
use App\Imports\SalesImport;
use App\Repositories\MasterRepository;

class BookStoreController extends ResponseController
{
    public function getAllBookStore(Request $request)
    {
        $bussiness = MasterRepository::BookStore($request);
        $data = $bussiness->getAllBookStore();
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function createBookStore(Request $request)
    {
        $bussiness = MasterRepository::BookStore($request);
        $data = $bussiness->createBookStore($request->all());
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function updateBookStore(Request $request, $id)
    {
        $bussiness = MasterRepository::BookStore($request, $id);
        $data = $bussiness->updateBookStore($request->all(), $id);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function deleteBookStore(Request $request, $id)
    {
        $bussiness = MasterRepository::BookStore($request, $id);
        $data = $bussiness->deleteBookStore($request, $id);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function toolCreateBookStore(Request $request)
    {
        $bussiness = MasterRepository::BookStore($request);
        $data = $bussiness->toolCreateBookStore($request->all());
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }


}
