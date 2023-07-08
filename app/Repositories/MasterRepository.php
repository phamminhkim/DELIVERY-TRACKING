<?php

namespace App\Repositories;

use App\Repositories\Master\WarehouseRepository;
use App\Repositories\Master\DeliveryPartnerRepository;
use App\Repositories\Master\CompanyRepository;
use App\Repositories\Master\CustomerRepository;
use App\Repositories\Master\EmployeeRepository;
use App\Repositories\Master\DistributionChannelRepository;
use App\Repositories\Master\SaleDistrictRepository;
use App\Repositories\Master\SaleGroupRepository;
use App\Repositories\Master\UserRepository;




use Illuminate\Http\Request;

class MasterRepository
{
    public static function warehouseRequest(Request $request)
    {
        return new WarehouseRepository($request);
    }

    public static function companyRequest(Request $request)
    {
        return new CompanyRepository($request);
    }
    public static function customerRequest(Request $request)
    {
        return new CustomerRepository($request);
    }
    public static function employeeRequest(Request $request)
    {
        return new EmployeeRepository($request);
    }
    public static function distributionChannelRequest(Request $request)
    {
        return new DistributionChannelRepository($request);
    }

    public static function saleGroupRequest(Request $request)
    {
        return new SaleGroupRepository($request);
    }
    public static function saleDistrictRequest(Request $request)
    {
        return new SaleDistrictRepository($request);
    }

    public static function deliveryPartnerRequest(Request $request)
    {
        return new DeliveryPartnerRepository($request);
    }
    public static function userRequest(Request $request)
    {
        return new UserRepository($request);
    }
}
