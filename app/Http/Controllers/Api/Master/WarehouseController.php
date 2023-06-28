<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use App\Repositories\MasterRepository;
use Illuminate\Http\Request;

class WarehouseController extends ResponseController
{
    public function getAvailableWarehouses(Request $request)
    {
        $handler = MasterRepository::warehouseRequest($request);
        $warehouses = $handler->getAvailableWarehouses();

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
