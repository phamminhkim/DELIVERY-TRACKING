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
        $bussiness = MasterRepository::getAllBookStore($request);
        $data = $bussiness->getAllBookStore();
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
}
