<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $fillable = [
    'company_id',
    'sap_do',
    'customer_id',
    'delivery_address',
    'weight',
    'note',
    'status_id',
    'delivery_id',
  ];

  protected $hidden = [

  ];
}