<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPartnerStore extends Model
{
    protected $fillable = ['name', 'code', 'is_deleted'];

}
