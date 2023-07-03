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
                $customers = $handler->getAvailableCustomers();
                // Kiểm tra xem $customers có phải là một mảng hay không
                if (!is_array($customers)) {
                    $customers = $customers->toArray();
                }
                $customer = $handler->createNewCustomer();                                               
                
            //lấy ra các thuộc tính cần lấy
            $filtered_customers = array_map(function ($customer) {
                return [
                    'code' => $customer['code'],
                    'name' => $customer['name'],
                    'email' => $customer['email'],
                    'phone_number' => $customer['phone_number'],
                    'address' => $customer['address'],
                ];
            }, $customers);
            return $this->responseSuccess($filtered_customers);
            case SapSyncCategory::DistributionChannel:
                $handler = MasterRepository::distributionChannelRequest($request);
                $distribution_channels = $handler->getAvailableDistributionChannels();

                if (!is_array($distribution_channels)) {
                    $distribution_channels = $distribution_channels->toArray();
                }
                $distribution_channel = $handler->createNewDistributionChannel();
                //$distributionChannel = $handler->updateExistingDistributionChannel($id);
                //lấy ra các thuộc tính cần lấy
                $filtered_distribution_channels = array_map(function ($distribution_channel) {
                    return [
                        'code' => $distribution_channel['code'],
                        'name' => $distribution_channel['name'],
                    ];
                }, $distribution_channels);
                return $this->responseSuccess($filtered_distribution_channels); 
            case SapSyncCategory::Warehouse:
                $handler = MasterRepository::warehouseRequest($request);
                $warehouses = $handler->getAvailableWarehouses();
            
                if (!is_array($warehouses)) {
                    $warehouses = $warehouses->toArray();
                }
                $warehouse = $handler->createNewWarehouse();
                //lấy ra các thuộc tính cần lấy
                $filtered_warehouses = array_map(function ($warehouse) {
                    return [
                        'code' => $warehouse['code'],
                        'name' => $warehouse['name'],
                    ];
                }, $warehouses);
                return $this->responseSuccess($filtered_warehouses); 
            case SapSyncCategory::SaleDistrict:
                $handler = MasterRepository::saleDistrictRequest($request);
                $saleDistricts = $handler->getAvailableSaleDistricts();
            
                if (!is_array($saleDistricts)) {
                    $saleDistricts = $saleDistricts->toArray();
                }          
                
                //$saleDistrict = $handler->createNewSaleDistrict();
                //lấy ra các thuộc tính cần lấy
                $filtered_saleDistricts = array_map(function ($saleDistrict) {
                    return [
                        'code' => $saleDistrict['code'],
                        'name' => $saleDistrict['name'],
                    ];
                }, $saleDistricts);
                return $this->responseSuccess($filtered_saleDistricts);
            case SapSyncCategory::SaleGroup:
                $handler = MasterRepository::saleGroupRequest($request);
                $saleGroups = $handler->getAvailableSaleGroups();
            
                if (!is_array($saleGroups)) {
                    $saleGroups = $saleGroups->toArray();
                }          
                
                //$saleDistrict = $handler->createNewSaleDistrict();
                //lấy ra các thuộc tính cần lấy
                $filtered_saleGroups = array_map(function ($saleGroup) {
                    return [
                        'code' => $saleGroup['code'],
                        'name' => $saleGroup['name'],
                    ];
                }, $saleGroups);
                return $this->responseSuccess($filtered_saleGroups);
            default:
                return $this->responseError('Invalid category', []);
        }
    }
}
