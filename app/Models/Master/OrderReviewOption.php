<?php

namespace App\Models\Master;

use App\Models\Business\OrderCustomerReviewCriteria;
use Illuminate\Database\Eloquent\Model;

class OrderReviewOption extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name'
    ];

    public function criteria()
    {
        return $this->hasMany(OrderCustomerReviewCriteria::class, 'review_option_id');
    }
}
