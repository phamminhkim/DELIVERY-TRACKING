<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use App\Http\Controllers\Controller;
use App\Repositories\MasterRepository;
use Illuminate\Http\Request;

class SapMaterialMappingController extends ResponseController
{
    public function createSapMateriasMappingsFromExcel(Request $request)
    {
        $handler = MasterRepository::sapMaterialMappingRequest($request);
        $result = $handler->createSapMateriasMappingsFromExcel();

        if ($result) {
            return $this->responseSuccess($result);
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
    public function exportToExcel(Request $request)
    {
        $handler = MasterRepository::sapMaterialMappingRequest($request);
        $sapMaterialMappings = $handler->exportToExcel();

        if ($sapMaterialMappings) {
            return $this->responseSuccess($sapMaterialMappings);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getAvailableSapMaterialMappings(Request $request)
    {

        $handler = MasterRepository::sapMaterialMappingRequest($request);
        $sapMaterialMappings = $handler->getAvailableSapMaterialMappings();

        if ($sapMaterialMappings) {
            return $this->responseSuccess($sapMaterialMappings);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
     //add
     public function createNewSapMaterialMappings(Request $request)
     {

         $handler = MasterRepository::sapMaterialMappingRequest($request);
         $sapMaterialMapping = $handler->createNewSapMaterialMappings($request->all());

         if ($sapMaterialMapping) {
             return $this->responseSuccess($sapMaterialMapping);
         } else {
             return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
         }
     }
     //update
     public function updateSapMaterialMapping(Request $request, $id)
{
    $handler = MasterRepository::sapMaterialMappingRequest($request);
    $sapMaterialMapping = $handler->updateSapMaterialMapping($id, $request->all());

    if ($sapMaterialMapping) {
        return $this->responseSuccess($sapMaterialMapping);
    } else {
        return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
    }
}
     public function deleteExistingSapMaterialMapping(Request $request, $id)
     {
         $handler = MasterRepository::sapMaterialMappingRequest($request);
         $is_success = $handler->deleteExistingSapMaterialMapping($id);

         if ($is_success) {
             return $this->responseOk();
         } else {
             $message = $handler->getMessage();
             $errors = $handler->getErrors();
             return $this->responseError($message, $errors);
         }
     }
}
