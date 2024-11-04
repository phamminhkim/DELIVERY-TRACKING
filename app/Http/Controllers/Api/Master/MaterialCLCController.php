<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\SapSyncCategory;
use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;


class MaterialCLCController extends ResponseController
{
    public function getAll(Request $request)
    {
        $handler = MasterRepository::materialCLCRequest($request);
        $material_clc = $handler->getAll(false);
        if ($material_clc) {
            return $this->response($material_clc);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function getAllMinified(Request $request)
    {

        $handler = MasterRepository::materialCLCRequest($request);
        $material_clc = $handler->getAll(true);

        if ($material_clc) {
            return $this->response($material_clc);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }


    public function createMaterialCLCFormExcel(Request $request)
    {
        $handler = MasterRepository::materialCLCRequest($request);
        $result = $handler->createMaterialCLCFormExcel();

        if ($result) {
            return $this->responseSuccess($result);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function exportToExcel(Request $request)
    {
        $handler = MasterRepository::materialCLCRequest($request);
        $material_clc = $handler->exportToExcel();

        if ($material_clc) {
            return $this->responseSuccess($material_clc);
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
        $handler = MasterRepository::materialCLCRequest($request);
        $material_clc = $handler->store($request);
        if ($material_clc) {
            return $this->response($material_clc);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }

    public function update(Request $request, $id)
    {
        $handler = MasterRepository::materialCLCRequest($request);
        $material_clc = $handler->update($id);
        if ($material_clc) {
            return $this->response($material_clc);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }

    public function destroy(Request $request, $id)
    {
        $handler = MasterRepository::materialCLCRequest($request);
        $material_clc = $handler->destroy($request, $id);
        if ($material_clc) {
            return $this->responseSuccess($material_clc);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function destroyMultiple(Request $request)
    {
        $handler = MasterRepository::materialCLCRequest($request);
        $ids = $request->input('ids');

        $material_clc = $handler->destroyMultiple($ids);

        if ($material_clc) {
            return $this->responseSuccess($material_clc);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
}
