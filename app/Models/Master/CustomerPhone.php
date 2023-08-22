<?php

namespace App\Models\Master;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;

class CustomerPhone extends Model
{
    use FullTextSearch;
    protected $fillable = [
        'customer_id', 'phone_number', 'name', 'description', 'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'is_receive_sms' => 'boolean',
    ];

    public $searchable = [
        'customer_phones.name', 
        'customer_phones.phone_number',
        'customer_phones.description'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
