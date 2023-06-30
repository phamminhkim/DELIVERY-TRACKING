<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class SaleGroupController extends ResponseController
{
    public function getAvailableSaleGroups(Request $request)
    {

        $handler = MasterRepository::saleGroupRequest($request);
        $saleGroups = $handler->getAvailableSaleGroups();

        if ($saleGroups) {
            return $this->responseSuccess($saleGroups);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add 
    public function createNewSaleGroup(Request $request)
    {

        $handler = MasterRepository::SaleGroupRequest($request);
        $saleGroup = $handler->createNewSaleGroup();

        if ($saleGroup) {
            return $this->responseSuccess($saleGroup);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingSaleGroup(Request $request, $id)
    {
        $handler = MasterRepository::SaleGroupRequest($request);
        $saleGroup = $handler->updateExistingSaleGroup($id);

        if ($saleGroup) {
            return $this->responseSuccess($saleGroup);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingSaleGroup(Request $request, $id)
    {
        $handler = MasterRepository::SaleGroupRequest($request);
        $is_success = $handler->deleteExistingSaleGroup($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
