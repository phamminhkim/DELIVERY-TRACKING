<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'code';

    protected $fillable = [
        'code',
        'name',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
