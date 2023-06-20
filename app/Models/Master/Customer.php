<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
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

    public function phones()
    {
        return $this->hasMany(CustomerPhone::class);
    }
}
