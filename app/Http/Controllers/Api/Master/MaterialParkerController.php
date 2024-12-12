<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\SapSyncCategory;
use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;


class MaterialParkerController extends ResponseController
{
    public function getAll(Request $request)
    {
        $handler = MasterRepository::materialParkerRequest($request);
        $material_parker = $handler->getAll(false);
        if ($material_parker) {
            return $this->response($material_parker);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function getAllMinified(Request $request)
    {

        $handler = MasterRepository::materialParkerRequest($request);
        $material_parker = $handler->getAll(true);

        if ($material_parker) {
            return $this->response($material_parker);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }


    public function createMaterialParkerFormExcel(Request $request)
    {
        $handler = MasterRepository::materialParkerRequest($request);
        $result = $handler->createMaterialParkerFormExcel();

        if ($result) {
            return $this->responseSuccess($result);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function exportToExcel(Request $request)
    {
        $handler = MasterRepository::materialParkerRequest($request);
        $material_parker = $handler->exportToExcel();

        if ($material_parker) {
            return $this->responseSuccess($material_parker);
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
        $handler = MasterRepository::materialParkerRequest($request);
        $material_parker = $handler->store($request);
        if ($material_parker) {
            return $this->response($material_parker);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }

    public function update(Request $request, $id)
    {
        $handler = MasterRepository::materialParkerRequest($request);
        $material_parker = $handler->update($id);
        if ($material_parker) {
            return $this->response($material_parker);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }

    public function destroy(Request $request, $id)
    {
        $handler = MasterRepository::materialParkerRequest($request);
        $material_parker = $handler->destroy($request, $id);
        if ($material_parker) {
            return $this->responseSuccess($material_parker);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function destroyMultiple(Request $request)
    {
        $handler = MasterRepository::materialParkerRequest($request);
        $ids = $request->input('ids');

        $material_parker = $handler->destroyMultiple($ids);

        if ($material_parker) {
            return $this->responseSuccess($material_parker);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
}
