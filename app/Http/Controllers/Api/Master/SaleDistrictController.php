<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class SaleDistrictController extends ResponseController
{
    public function getAvailableSaleDistricts(Request $request)
    {

        $handler = MasterRepository::saleDistrictRequest($request);
        $saleDistricts = $handler->getAvailableSaleDistricts();

        if ($saleDistricts) {
            return $this->responseSuccess($saleDistricts);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add 
    public function createNewSaleDistrict(Request $request)
    {

        $handler = MasterRepository::SaleDistrictRequest($request);
        $saleDistrict = $handler->createNewSaleDistrict();

        if ($saleDistrict) {
            return $this->responseSuccess($saleDistrict);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingSaleDistrict(Request $request, $id)
    {
        $handler = MasterRepository::SaleDistrictRequest($request);
        $saleDistrict = $handler->updateExistingSaleDistrict($id);

        if ($saleDistrict) {
            return $this->responseSuccess($saleDistrict);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingSaleDistrict(Request $request, $id)
    {
        $handler = MasterRepository::SaleDistrictRequest($request);
        $is_success = $handler->deleteExistingSaleDistrict($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
