<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class DistributionChannel extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'code',
        'name'
    ];
}
