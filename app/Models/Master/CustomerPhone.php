<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerPhone extends Model
{
    protected $fillable = [
        'customer_id', 'phone_number', 'name', 'description', 'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'is_receive_sms' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
