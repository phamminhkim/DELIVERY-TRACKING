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
        'code', 
        'name', 
        'email',
        'phone_number',
        'address'
    ];
    public function phones()
    {
        return $this->hasMany(CustomerPhone::class);
    }

}
