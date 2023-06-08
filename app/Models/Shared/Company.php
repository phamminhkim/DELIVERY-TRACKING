<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name', 'id', 'active'
    ];
    protected $casts = [
        'id' => 'string'
    ];
}
