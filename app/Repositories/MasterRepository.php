<?php

namespace App\Repositories;

use App\Repositories\Master\WarehouseRepository;
use App\Repositories\Master\DeliveryPartnerRepository;
use App\Repositories\Master\CompanyRepository;
use App\Repositories\Master\CustomerRepository;
use App\Repositories\Master\EmployeeRepository;

use Illuminate\Http\Request;

class MasterRepository
{
    public static function warehouseRequest(Request $request)
    {
        return new WarehouseRepository($request);
    }
    public static function deliveryPartnerRequest(Request $request)
    {
        return new DeliveryPartnerRepository($request);
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
}
