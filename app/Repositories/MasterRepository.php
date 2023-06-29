<?php

namespace App\Repositories;

use App\Repositories\Master\WarehouseRepository;
use App\Repositories\Master\DeliveryPartnerRepository;
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
}
