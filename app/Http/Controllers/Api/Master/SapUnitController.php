<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use App\Repositories\MasterRepository;
use Illuminate\Http\Request;

class SapUnitController extends ResponseController
{
    public function getAvailableSapUnits(Request $request)
    {

        $handler = MasterRepository::sapUnitRequest($request);
        $sapUnits = $handler->getAvailableSapUnits();

        if ($sapUnits) {
            return $this->responseSuccess($sapUnits);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add
    public function createNewSapUnit(Request $request)
    {

        $handler = MasterRepository::sapUnitRequest($request);
        $sapUnit = $handler->createNewSapUnit();

        if ($sapUnit) {
            return $this->responseSuccess($sapUnit);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingSapUnit(Request $request, $id)
    {
        $handler = MasterRepository::sapUnitRequest($request);
        $sapUnit = $handler->updateExistingSapUnit($id);

        if ($sapUnit) {
            return $this->responseSuccess($sapUnit);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingSapUnit(Request $request, $id)
    {
        $handler = MasterRepository::sapUnitRequest($request);
        $is_success = $handler->deleteExistingSapUnit($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
