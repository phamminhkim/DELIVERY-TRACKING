<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderCustomerReview extends Model
{
    protected $fillable = [
        'review_content',
    ];
    protected $hidden = [
        'order_id',
        'customer_id',
        'created_at',
        'updated_at'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function criteria()
    {
        return $this->belongsToMany(CustomerReviewCriteria::class, 'order_customer_review_criteria', 'review_id', 'criteria_id');
    }
}
