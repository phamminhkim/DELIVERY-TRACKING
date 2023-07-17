<?php

namespace App\Repositories\System;

use App\Models\System\Route;
use App\Repositories\Abstracts\RepositoryAbs;

class RouteRepository extends RepositoryAbs
{
    public function getRoutes()
    {
        $routes = Route::all();
        return $routes;
    }
}
