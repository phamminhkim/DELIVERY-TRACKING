<?php

namespace App\Repositories;

use App\Repositories\Master\WarehouseRepository;
use Illuminate\Http\Request;

class MasterRepository
{
    public static function warehouseRequest(Request $request)
    {
        return new WarehouseRepository($request);
    }
}
