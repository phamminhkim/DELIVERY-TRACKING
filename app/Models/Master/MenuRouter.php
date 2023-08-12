<?php

namespace App\Models\Master;

use App\Models\System\Role;
use App\Models\System\Route;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class MenuRouter extends Model
{
    use HasRoles;
    public $timestamps = false;
    protected $fillable = [
        'title',
        'route_id',
        'parent_id',
        'order',
        'link',
        'query_string',
        'icon',
        'left',
        'right',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function children()
    {
        return $this->hasMany(MenuRouter::class, 'parent_id', 'id')->orderBy('order');
    }

    public function parents()
    {
        return $this->hasOne(MenuRouter::class, 'id', 'parent_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menu', 'menu_id', 'role_id')->withTimestamps();
    }
}
