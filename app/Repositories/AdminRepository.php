<?php

namespace App\Repositories;

use App\Repositories\Admin\RoleRepository;
use Illuminate\Http\Request;

class AdminRepository
{
    public static function roleRequest(Request $request)
    {
        return new RoleRepository($request);
    }
}
