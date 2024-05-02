<?php
namespace App\Models\Master;
use App\Traits\FullTextSearch;

use Illuminate\Database\Eloquent\Model;

class CustomerPartner extends Model
{
    use FullTextSearch;
    protected $table = 'customer_partners';

    protected $fillable = [
        'customer_group_id',
        'name',
        'code',
        'note',
        'LV2',
        'LV3',
        'LV4',
        'is_deleted',
    ];
    protected $searchable = [
        'customer_partners.code',
        'customer_partners.name',
        'customer_partners.note',
    ];
    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
