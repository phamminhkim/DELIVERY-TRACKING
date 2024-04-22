<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerPartner extends Model
{
    protected $table = 'customer_partners';

    protected $fillable = [
        'name',
        'code',
        'note',
        'LV2',
        'LV3',
        'LV4',
        'is_deleted',
    ];
}
