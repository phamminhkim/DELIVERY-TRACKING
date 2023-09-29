<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class RegexPattern extends Model
{
    protected $fillable = [
        'name',
        'pattern'
    ];

    public $timestamps = false;
}
