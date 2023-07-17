<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'path', 'component'
    ];
    protected $hidden = [
        'id'
    ];
}
