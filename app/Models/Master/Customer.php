<?php

namespace App\Models\Master;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    use FullTextSearch;
    protected $fillable = [
        'code',
        'name',
        'email',
        'phone_number',
        'address',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $searchable = [
        'customers.code',
        'customers.name',
        'customers.email',
        'customers.phone_number',
    ];
    public function phones()
    {
        return $this->hasMany(CustomerPhone::class);
    }

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_pivots', 'customer_id', 'customer_group_id');
    }
}
