<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class SapMaterialController extends ResponseController
{
    public function getAvailableSapMaterials(Request $request)
    {

        $handler = MasterRepository::sapMaterialRequest($request);
        $sapMaterials = $handler->getAvailableSapMaterials();

        if ($sapMaterials) {
            return $this->responseSuccess($sapMaterials);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add
    public function createNewSapMaterial(Request $request)
    {

        $handler = MasterRepository::sapMaterialRequest($request);
        $sapMaterial = $handler->createNewSapMaterial();

        if ($sapMaterial) {
            return $this->responseSuccess($sapMaterial);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingSapMaterial(Request $request, $id)
    {
        $handler = MasterRepository::sapMaterialRequest($request);
        $sapMaterial = $handler->updateExistingSapMaterial($id);

        if ($sapMaterial) {
            return $this->responseSuccess($sapMaterial);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingSapMaterial(Request $request, $id)
    {
        $handler = MasterRepository::sapMaterialRequest($request);
        $is_success = $handler->deleteExistingSapMaterial($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
