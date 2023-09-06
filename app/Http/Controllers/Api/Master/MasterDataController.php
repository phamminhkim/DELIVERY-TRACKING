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
        $handler = null;
        switch ($category) {
            case SapSyncCategory::Customer:
                $handler = MasterRepository::customerRequest($request);
                break;

            case SapSyncCategory::DistributionChannel:
                $handler = MasterRepository::distributionChannelRequest($request);
                break;

            case SapSyncCategory::Warehouse:
                $handler = MasterRepository::warehouseRequest($request);
                break;

            case SapSyncCategory::SaleDistrict:
                $handler = MasterRepository::saleDistrictRequest($request);
                break;

            case SapSyncCategory::SaleGroup:
                $handler = MasterRepository::saleGroupRequest($request);
                break;

            case SapSyncCategory::SapMaterial:
                $handler = MasterRepository::sapMaterialRequest($request);
                break;

            default:
                return $this->responseError('Invalid category', []);
        }

        if ($handler) {
            $return_data = $handler->updateOrInsert();
            if ($return_data) {
                return $this->responseSuccess($return_data);
            } else {
                return $this->responseError($handler->getMessage(), $handler->getErrors());
            }
        }
    }
}
