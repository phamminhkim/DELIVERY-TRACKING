<?php

namespace App\Repositories;

use App\Models\Master\CustomerMaterial;
use App\Models\Master\SapMaterial;
use App\Repositories\Master\CustomerGroupRepository;
use App\Repositories\Master\SapMaterialMappingRepository;
use App\Repositories\Master\WarehouseRepository;
use App\Repositories\Master\DeliveryPartnerRepository;
use App\Repositories\Master\CompanyRepository;
use App\Repositories\Master\CustomerRepository;
use App\Repositories\Master\EmployeeRepository;
use App\Repositories\Master\DistributionChannelRepository;
use App\Repositories\Master\MenuRouterRepository;
use App\Repositories\Master\SaleDistrictRepository;
use App\Repositories\Master\SaleGroupRepository;
use App\Repositories\Master\UserRepository;
use App\Repositories\Master\CustomerPhoneRepository;
use App\Repositories\Master\OrderReviewOptionRepository;
use App\Repositories\Master\SapMaterialRepository;
use App\Repositories\Master\SapUnitRepository;
use App\Repositories\Master\CustomerMaterialRepository;
use App\Repositories\Master\CustomerGroupPivotRepository;
use App\Repositories\Master\CustomerPromotionRepository;
use App\Repositories\Master\MaterialCategoryTypeRepository;
use App\Repositories\Master\MaterialComboRepository;
use App\Repositories\Master\MaterialDonatedRepository;
use App\Repositories\Master\CustomerPartnerRepository;
use App\Repositories\Master\SapComplianceRepository;
use App\Repositories\Master\UserFieldTableRepository;
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
    public static function sapMaterialRequest(Request $request)
    {
        return new SapMaterialRepository($request);
    }

    public static function sapMaterialMappingRequest(Request $request)
    {
        return new SapMaterialMappingRepository($request);
    }

    public static function customerMaterialRequest(Request $request)
    {
        return new CustomerMaterialRepository($request);
    }
    public static function sapUnitRequest(Request $request)
    {
        return new SapUnitRepository($request);
    }

    public static function deliveryPartnerRequest(Request $request)
    {
        return new DeliveryPartnerRepository($request);
    }
    public static function userRequest(Request $request)
    {
        return new UserRepository($request);
    }
    public static function menuRouterRequest(Request $request)
    {
        return new MenuRouterRepository($request);
    }
    public static function customerPhoneRequest(Request $request)
    {
        return new CustomerPhoneRepository($request);
    }
    public static function orderReviewOptionRequest(Request $request)
    {
        return new OrderReviewOptionRepository($request);
    }

    public static function customerGroupRequest(Request $request)
    {
        return new CustomerGroupRepository($request);
    }
    public static function customerGroupPivotRequest(Request $request)
    {
        return new CustomerGroupPivotRepository($request);
    }
    public static function customerPromotionRequest(Request $request)
    {
        return new CustomerPromotionRepository($request);
    }
    public static function materialCategoryTypeRequest(Request $request)
    {
        return new MaterialCategoryTypeRepository($request);
    }
    public static function materialDonatedRequest(Request $request)
    {
        return new MaterialDonatedRepository($request);
    }
    public static function materialComboRequest(Request $request)
    {
        return new MaterialComboRepository($request);
    }
    public static function customerPartnerRequest(Request $request)
    {
        return new CustomerPartnerRepository($request);
    }
    public static function sapComplianceRequest(Request $request)
    {
        return new SapComplianceRepository($request);
    }
    public static function userFieldTableRequest(Request $request)
    {
        return new UserFieldTableRepository($request);
    }

}
