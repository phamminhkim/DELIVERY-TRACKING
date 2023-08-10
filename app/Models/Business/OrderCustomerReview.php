<?php

namespace App\Models\Business;

use App\Models\Master\Image;
use App\Models\Master\OrderReviewOption;
use App\User;
use Illuminate\Database\Eloquent\Model;

class OrderCustomerReview extends Model
{
    protected $fillable = [
        'review_content',
        'user_id'
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
     public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function criterias()
    {
         return $this->belongsToMany(OrderReviewOption::class, 'order_customer_review_criterias', 'review_id', 'criteria_id')
                    ->withPivot('id')
                    ->as('pivot');
    }
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable', 'imageable_type', 'imageable_id', 'id');
    }
}
