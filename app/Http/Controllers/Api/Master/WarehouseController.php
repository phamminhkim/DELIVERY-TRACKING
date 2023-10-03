<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;


class WarehouseController extends ResponseController
{
    public function getAvailableWarehouses(Request $request)
    {
        $handler = MasterRepository::warehouseRequest($request);
        $warehouses = $handler->getAvailableWarehouses(false, false);

        if ($warehouses) {
            return $this->responseSuccess($warehouses);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function getAvailableWarehousesMinified(Request $request)
    {
        $handler = MasterRepository::warehouseRequest($request);
        $warehouses = $handler->getAvailableWarehouses(true, false); // Set is_minified = true, is_active = false

        if ($warehouses) {
            return $this->responseSuccess($warehouses);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function getAvailableWarehousesActive(Request $request)
    {
        $handler = MasterRepository::warehouseRequest($request);
        $warehouses = $handler->getAvailableWarehouses(false, true); // Set is_minified = false, is_active = true

        if ($warehouses) {
            return $this->responseSuccess($warehouses);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function createNewWarehouse(Request $request)
    {
        $handler = MasterRepository::warehouseRequest($request);
        $warehouse = $handler->createNewWarehouse();

        if ($warehouse) {
            return $this->responseSuccess($warehouse);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function updateExistingWarehouse(Request $request, $id)
    {
        $handler = MasterRepository::warehouseRequest($request);
        $warehouse = $handler->updateExistingWarehouse($id);

        if ($warehouse) {
            return $this->responseSuccess($warehouse);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingWarehouse(Request $request, $id)
    {
        $handler = MasterRepository::warehouseRequest($request);
        $warehouse = $handler->deleteExistingWarehouse($id);

        if ($warehouse) {
            return $this->responseSuccess($warehouse);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
