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
        $is_success = $handler->createSapMateriasMappingsFromExcel();

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
