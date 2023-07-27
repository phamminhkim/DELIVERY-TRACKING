<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderCustomerReviewCriteria extends Model
{
    protected $table = 'order_customer_review_criterias';
    protected $hidden = [
        'review_id',
        'criteria_id',
        'created_at',
        'updated_at'
    ];
    public function review()
    {
        return $this->belongsTo(OrderCustomerReview::class);
    }
    public function criteria()
    {
        return $this->belongsTo(CustomerReviewCriteria::class);
    }
}
