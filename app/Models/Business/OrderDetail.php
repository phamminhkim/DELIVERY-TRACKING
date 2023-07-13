<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'delivery_address', 'note', 'total_item', 'total_weight', 'total_value'
    ];
    protected $hidden = [
        'order_id',
        'created_at',
        'updated_at'
    ];
    public function getTotalValueAttribute($value)
    {
        // Check if the decimal part is zero
        if (floor($value) == $value) {
            $formatted_value = number_format($value);
        } else {
            $formatted_value = number_format($value, 2);
        }

        return $formatted_value;
    }
}
