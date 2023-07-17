<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'badge_class',
    ];
}
