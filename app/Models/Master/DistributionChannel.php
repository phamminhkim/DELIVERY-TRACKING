<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class DistributionChannel extends Model
{
    protected $fillable = [
        'code',
        'name'
    ];
}
