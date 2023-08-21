<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\SapSyncCategory;
use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;
use Illuminate\Support\Facades\Validator;

class MasterDataController extends ResponseController
{
    public function syncFromSAP(Request $request, $category)
    {
        switch ($category) {
            case SapSyncCategory::Customer:
                $handler = MasterRepository::customerRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                return $data;

            case SapSyncCategory::DistributionChannel:
                $handler = MasterRepository::distributionChannelRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                return $data;

            case SapSyncCategory::Warehouse:
                $handler = MasterRepository::warehouseRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                return $data;

            case SapSyncCategory::SaleDistrict:
                $handler = MasterRepository::saleDistrictRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                return $data;

            case SapSyncCategory::SaleGroup:
                $handler = MasterRepository::saleGroupRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                return $data;

            case SapSyncCategory::SapMaterial:
                $handler = MasterRepository::sapMaterialRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                return $data;

            default:
                return $this->responseError('Invalid category', []);
        }
    }
}
