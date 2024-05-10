<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\SapSyncCategory;
use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;


class MaterialComboController extends ResponseController
{
    public function getAll(Request $request)
    {
        $handler = MasterRepository::materialComboRequest($request);
        $material_combo = $handler->getAll(false);
        if ($material_combo) {
            return $this->response($material_combo);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
        }

    }
    public function getAllMinified(Request $request)
    {

        $handler = MasterRepository::materialComboRequest($request);
        $material_combo = $handler->getAll(true);

        if ($material_combo) {
            return $this->response($material_combo);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
        }
    }


    public function createMaterialComboFormExcel(Request $request)
    {
        $handler = MasterRepository::materialComboRequest($request);
        $result = $handler->createMaterialComboFormExcel();

        if ($result) {
            return $this->responseSuccess($result);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function exportToExcel(Request $request)
    {
        $handler = MasterRepository::materialComboRequest($request);
        $material_combo = $handler->exportToExcel();

        if ($material_combo) {
            return $this->responseSuccess($material_combo);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function download($filename)
    {
        $filePath = public_path('excel/' . $filename);

        if (file_exists($filePath)) {
            return Response::download($filePath);
        }
    }

    public function store(Request $request)
    {
        $handler = MasterRepository::materialComboRequest($request);
        $material_combo = $handler->store($request);
        if ($material_combo) {
            return $this->response($material_combo);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
        }
    }

    public function update(Request $request, $id)
    {
        $handler = MasterRepository::materialComboRequest($request);
        $material_combo = $handler->update($id);
        if ($material_combo) {
            return $this->response($material_combo);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(),200);
        }
    }

    public function destroy(Request $request, $id)
    {
        $handler = MasterRepository::materialComboRequest($request);
        $material_combo = $handler->destroy($request, $id);
        if ($material_combo) {
            return $this->responseSuccess($material_combo);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
