<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\SapSyncCategory;
use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Validator;

class MaterialDonatedController extends ResponseController
{

    public function getAll(Request $request)
    {
        $handler = MasterRepository::materialDonatedRequest($request);
        $materialDonated = $handler->getAll($request);
        if ($materialDonated) {
            return $this->responseSuccess($materialDonated);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
        }

    }
    public function getAllMinified(Request $request)
    {

        $handler = MasterRepository::materialDonatedRequest($request);
        $materialDonated = $handler->getAll(true);

        if ($materialDonated) {
            return $this->response($materialDonated);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
        }
    }

    public function store(Request $request)
    {
        $handler = MasterRepository::materialDonatedRequest($request);
        $material_donated = $handler->store($request);
        if ($material_donated) {
            return $this->responseSuccess($material_donated);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
        }
    }

    public function update(Request $request, $id)
    {
        $handler = MasterRepository::materialDonatedRequest($request);
        $material_donated = $handler->update($request, $id);
        if ($material_donated) {
            return $this->responseSuccess($material_donated);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
        }
    }

    public function destroy(Request $request, $id)
    {
        $handler = MasterRepository::materialDonatedRequest($request);
        $material_donated = $handler->destroy($request, $id);
        if ($material_donated) {
            return $this->responseSuccess($material_donated);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
