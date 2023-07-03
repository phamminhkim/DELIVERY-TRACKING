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
                //dd("abc");
                $handler = MasterRepository::customerRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                break;               
        
            case SapSyncCategory::DistributionChannel:
                $handler = MasterRepository::distributionChannelRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                break;
            case SapSyncCategory::Warehouse:
                //dd("Warehouse");
                $handler = MasterRepository::warehouseRequest($request);
                $data = $request->all();   
                $handler->updateOrInsert();             
                break;
            case SapSyncCategory::SaleDistrict:
                //dd("SaleDistrict");
                $handler = MasterRepository::saleDistrictRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                break; 
            case SapSyncCategory::SaleGroup:
                //dd("SaleGroup");
                $handler = MasterRepository::saleGroupRequest($request);
                $data = $request->all();
                $handler->updateOrInsert();
                break;
            default:
                return $this->responseError('Invalid category', []);
        }
    }
}
