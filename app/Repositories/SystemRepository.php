<?php

namespace App\Repositories;

use App\Repositories\System\ZaloRepository;
use Illuminate\Http\Request;

class SystemRepository
{
    public static function zaloRequest(Request $request)
    {
        return new ZaloRepository($request);
    }
}
