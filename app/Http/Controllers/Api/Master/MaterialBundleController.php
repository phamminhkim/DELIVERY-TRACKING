<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\SapSyncCategory;
use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;


class MaterialBundleController extends ResponseController
{
    public function getAll(Request $request)
    {
        $handler = MasterRepository::materialBundleRequest($request);
        $material_bundle = $handler->getAll(false);
        if ($material_bundle) {
            return $this->response($material_bundle);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function getAllMinified(Request $request)
    {

        $handler = MasterRepository::materialBundleRequest($request);
        $material_bundle = $handler->getAll(true);

        if ($material_bundle) {
            return $this->response($material_bundle);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }


    public function createMaterialBundleFormExcel(Request $request)
    {
        $handler = MasterRepository::materialBundleRequest($request);
        $result = $handler->createMaterialBundleFormExcel();

        if ($result) {
            return $this->responseSuccess($result);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
    public function exportToExcel(Request $request)
    {
        $handler = MasterRepository::materialBundleRequest($request);
        $material_bundle = $handler->exportToExcel();

        if ($material_bundle) {
            return $this->responseSuccess($material_bundle);
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
        $handler = MasterRepository::materialBundleRequest($request);
        $material_bundle = $handler->store($request);
        if ($material_bundle) {
            return $this->response($material_bundle);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }

    public function update(Request $request, $id)
    {
        $handler = MasterRepository::materialBundleRequest($request);
        $material_bundle = $handler->update($id);
        if ($material_bundle) {
            return $this->response($material_bundle);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }

    public function destroy(Request $request, $id)
    {
        $handler = MasterRepository::materialBundleRequest($request);
        $material_bundle = $handler->destroy($request, $id);
        if ($material_bundle) {
            return $this->responseSuccess($material_bundle);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function destroyMultiple(Request $request)
    {
        $handler = MasterRepository::materialBundleRequest($request);
        $ids = $request->input('ids');

        $material_bundle = $handler->destroyMultiple($ids);

        if ($material_bundle) {
            return $this->responseSuccess($material_bundle);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors(), 200);
        }
    }
}
