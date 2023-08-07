<?php

namespace App\Repositories\System;

use App\Models\System\Route;
use App\Repositories\Abstracts\RepositoryAbs;

class RouteRepository extends RepositoryAbs
{
    public function getRoutes()
    {
        $query = Route::query();
        $routes = $query->get();
        $result = array();

        if ($this->request->filled('format')) {
            if ($this->request->format == 'treeselect') {
                    foreach ($routes as $route) {
                        $item = array(
                            'id' => $route->id,
                            'label' => $route->path . ' (' . $route->name . ')',
                            'object' => $route
                        );
                        array_push($result, $item);
                    }
            }
        }
        else {
            $result = $routes;
        }
        return $result;
    }
}
