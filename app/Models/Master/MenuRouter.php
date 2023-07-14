<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MenuRouter extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title',
        'parent_id',
        'order',
        'link',
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
}
