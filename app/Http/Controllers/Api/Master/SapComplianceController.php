<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Response;

class SapComplianceController extends ResponseController
{
    public function getAvailableSapCompliances(Request $request)
    {

        $handler = MasterRepository::sapComplianceRequest($request);
        $sapCompliances = $handler->getAvailableSapCompliances(false, $request);

        if ($sapCompliances) {
            return $this->response($sapCompliances);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getAvailableSapCompliancesMinified(Request $request)
    {
        $handler = MasterRepository::sapComplianceRequest($request);
        $sapCompliances = $handler->getAvailableSapCompliances(true, $request);

        if ($sapCompliances) {
            return $this->response($sapCompliances);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function createSapComplianceFormExcel(Request $request)
    {
        $handler = MasterRepository::sapComplianceRequest($request);
        $result = $handler->createSapComplianceFormExcel();

        if ($result) {
            return $this->response($result);
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
    public function createNewSapCompliance(Request $request)
    {

        $handler = MasterRepository::sapComplianceRequest($request);
        $sap_compliance = $handler->createNewSapCompliance();

        if ($sap_compliance) {
            return $this->response($sap_compliance);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    //update
    public function updateExistingSapCompliance(Request $request, $id)
    {
        $handler = MasterRepository::sapComplianceRequest($request);
        $sapCompliance = $handler->updateExistingSapCompliance($id);

        if ($sapCompliance) {
            return $this->response($sapCompliance);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function deleteExistingSapCompliance(Request $request, $id)
    {
        $handler = MasterRepository::sapComplianceRequest($request);
        $is_success = $handler->deleteExistingSapCompliance($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
