<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerGroupPivot extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'customer_group_id'
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
