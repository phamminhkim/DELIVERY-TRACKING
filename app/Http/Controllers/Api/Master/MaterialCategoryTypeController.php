<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\SapSyncCategory;
use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Validator;

class MaterialCategoryTypeController extends ResponseController
{
    public function getAll(Request $request)
    {
        $handler = MasterRepository::materialCategoryTypeRequest($request);
        $materialCategoryTypes = $handler->getAll($request);
        if ($materialCategoryTypes) {
            return $this->responseSuccess($materialCategoryTypes);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }

    }

    public function store(Request $request)
    {
        $handler = MasterRepository::materialCategoryTypeRequest($request);
        $materialCategoryType = $handler->store($request);
        if ($materialCategoryType) {
            return $this->responseSuccess($materialCategoryType);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function update(Request $request, $id)
    {
        $handler = MasterRepository::materialCategoryTypeRequest($request);
        $materialCategoryType = $handler->update($request, $id);
        if ($materialCategoryType) {
            return $this->responseSuccess($materialCategoryType);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function delete(Request $request, $id)
    {
        $handler = MasterRepository::materialCategoryTypeRequest($request);
        $materialCategoryType = $handler->delete($request, $id);
        if ($materialCategoryType) {
            return $this->responseSuccess($materialCategoryType);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
