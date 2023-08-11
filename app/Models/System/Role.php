<?php 

namespace App\Models\System;

use App\Models\Master\MenuRouter;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function menus()
    {
        return $this->belongsToMany(MenuRouter::class, 'role_menu', 'id', 'menu_id');
    }
}