<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerMaterial extends Model
{
    public $timestamps = false;

    //fillable
    protected $fillable = [
        'customer_group_id',
        'po_code',
        'name',
        'unit'
    ];

    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
