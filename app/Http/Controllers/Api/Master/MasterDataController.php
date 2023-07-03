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
                // if (isset($data['id'])) {
                //     $id = $data['id'];
                //     $customer = $handler->updateExistingCustomer($id);
                
                //     if ($customer) {
                //         return $this->responseSuccess($customer);
                //     } else {
                //         return $this->responseError($handler->getMessage(), $handler->getErrors());
                //     }
                // } else {
                //     $customer = $handler->createNewCustomer($data);
                
                //     if ($customer) {
                //         return $this->responseSuccess($customer);
                //     } else {
                //         return $this->responseError($handler->getMessage(), $handler->getErrors());
                //     }
                // }     
        
            case SapSyncCategory::DistributionChannel:
                //dd("DistributionChannel");
                // $handler = MasterRepository::districtbutionChannelRequest($request);
                // $data = $request->all();
                
                // if (isset($data['id'])) {
                //     $id = $data['id'];
                //     $distribution_channel = $handler->updateExistingDistributionChannel($id);
                
                //     if ($distribution_channel) {
                //         return $this->responseSuccess($distribution_channel);
                //     } else {
                //         return $this->responseError($handler->getMessage(), $handler->getErrors());
                //     }
                // } else {
                //     $distribution_channel = $handler->createNewDistributionChannel($data);
                
                //     if ($distribution_channel) {
                //         return $this->responseSuccess($distribution_channel);
                //     } else {
                //         return $this->responseError($handler->getMessage(), $handler->getErrors());
                //     }
                // }     
                break;
            case SapSyncCategory::Warehouse:
                //dd("Warehouse");
                $handler = MasterRepository::warehouseRequest($request);
                $data = $request->all();
                
                if (isset($data['id'])) {
                    $id = $data['id'];
                    $warehouse = $handler->updateExistingWarehouse($id);
                
                    if ($warehouse) {
                        return $this->responseSuccess($warehouse);
                    } else {
                        return $this->responseError($handler->getMessage(), $handler->getErrors());
                    }
                } else {
                    $warehouse = $handler->createNewWarehouse($data);
                
                    if ($warehouse) {
                        return $this->responseSuccess($warehouse);
                    } else {
                        return $this->responseError($handler->getMessage(), $handler->getErrors());
                    }
                }     
                break;
            case SapSyncCategory::SaleDistrict:
                //dd("SaleDistrict");
                $handler = MasterRepository::saleDistrictRequest($request);
                $data = $request->all();
                
                if (isset($data['id'])) {
                    $id = $data['id'];
                    $saleDistrict = $handler->updateExistingSaleDistrict($id);
                
                    if ($saleDistrict) {
                        return $this->responseSuccess($saleDistrict);
                    } else {
                        return $this->responseError($handler->getMessage(), $handler->getErrors());
                    }
                } else {
                    $saleDistrict = $handler->createNewSaleDistrict($data);
                
                    if ($saleDistrict) {
                        return $this->responseSuccess($saleDistrict);
                    } else {
                        return $this->responseError($handler->getMessage(), $handler->getErrors());
                    }
                }    
                break; 
            case SapSyncCategory::SaleGroup:
                //dd("SaleGroup");
                $handler = MasterRepository::saleGroupRequest($request);
                $data = $request->all();
                
                if (isset($data['id'])) {
                    $id = $data['id'];
                    $saleGroup = $handler->updateExistingSaleGroup($id);
                
                    if ($saleGroup) {
                        return $this->responseSuccess($saleGroup);
                    } else {
                        return $this->responseError($handler->getMessage(), $handler->getErrors());
                    }
                } else {
                    $saleGroup = $handler->createNewSaleGroup($data);
                
                    if ($saleGroup) {
                        return $this->responseSuccess($saleGroup);
                    } else {
                        return $this->responseError($handler->getMessage(), $handler->getErrors());
                    }
                }     
                break;
            default:
                return $this->responseError('Invalid category', []);
        }
    }
}
