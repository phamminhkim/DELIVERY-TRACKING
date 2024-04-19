<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\SapSyncCategory;
use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Validator;

class MaterialCategoryTypeController extends ResponseController
{
    public function getAvailableCategoryTypes(Request $request)
    {
        $handler = MasterRepository::materialCategoryTypeRequest($request);
        $materialCategoryTypes = $handler->getAvailableCategoryTypes($request);
        if ($materialCategoryTypes) {
            return $this->response($materialCategoryTypes);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }

    }

    public function createNewCategoryType(Request $request)
    {
        $handler = MasterRepository::materialCategoryTypeRequest($request);
        $materialCategoryType = $handler->createNewCategoryType($request);
        if ($materialCategoryType) {
            return $this->response($materialCategoryType);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function updateExistingCategoryType(Request $request, $id)
    {
        $handler = MasterRepository::materialCategoryTypeRequest($request);
        $materialCategoryType = $handler->updateExistingCategoryType($request, $id);
        if ($materialCategoryType) {
            return $this->response($materialCategoryType);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function deleteExistingCategoryType(Request $request, $id)
    {
        $handler = MasterRepository::materialCategoryTypeRequest($request);
        $materialCategoryType = $handler->deleteExistingCategoryType($request, $id);
        if ($materialCategoryType) {
            return $this->response($materialCategoryType);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
