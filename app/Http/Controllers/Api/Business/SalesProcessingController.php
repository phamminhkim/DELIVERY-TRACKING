<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\BaseController\ResponseController;
use App\Imports\SalesImport;
use App\Repositories\BusinessRepository;

class SalesProcessingController extends ResponseController
{
    public function importSales(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:20480'
        ]);
        if ($validator->fails()) {
            return $this->responseError('Validation Error', $validator->errors());
        }

        $file = $request->file('file');
        $import = new SalesImport;
        Excel::import($import, $file);
        // dd($import->getData(),'123');
        // if ($import->failures()->isNotEmpty()) {
        //     return $this->responseError('Import Error', $import->failures());
        // }
        return response()->json([
            'success' => 1,
            'data' => $import->getData(),
            'message' => 'Chuyển đổi dữ liệu thành công'
        ]);

        // return $this->responseSuccess('Import Success');
    }
    public function checkSapCode(Request $request){
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->checkMaterialSap($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function saveSales(Request $request){
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->saveSales($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function checkBookStore(Request $request){
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->checkBookStore($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function checkSapCompliance(Request $request){
        $bussiness = BusinessRepository::SOProcessingOrder($request);
        $data = $bussiness->checkSapCompliance($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
    public function processingSOData(Request $request){
        $bussiness = BusinessRepository::processingSOData($request);
        $data = $bussiness->processingSOData($request->items);
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($bussiness->getMessage(), $bussiness->getErrors());
        }
    }
}
