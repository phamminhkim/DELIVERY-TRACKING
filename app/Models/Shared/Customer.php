<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'id', 'active','user_id'
    ];
}
