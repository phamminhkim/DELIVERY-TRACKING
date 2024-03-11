<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Response;

class SapMaterialController extends ResponseController
{
    public function getAvailableSapMaterials(Request $request)
    {

        $handler = MasterRepository::sapMaterialRequest($request);
        $sapMaterials = $handler->getAvailableSapMaterials(false, $request);

        if ($sapMaterials) {
            return $this->responseSuccess($sapMaterials);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getAvailableSapMaterialsMinified(Request $request)
    {
        $handler = MasterRepository::sapMaterialRequest($request);
        $sapMaterials = $handler->getAvailableSapMaterials(true, $request);

        if ($sapMaterials) {
            return $this->responseSuccess($sapMaterials);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function createSapMaterialFormExcel(Request $request)
    {
        $handler = MasterRepository::sapMaterialRequest($request);
        $result = $handler->createSapMaterialFormExcel();

        if ($result) {
            return $this->responseSuccess($result);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function download($filename)
    {
        $filePath = public_path('excel/' . $filename);

        if (file_exists($filePath)) {
            return Response::download($filePath);
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
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
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
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
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
