<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MaterialDonated extends Model
{
    protected $table = 'material_donateds';
    protected $fillable = ['sap_code', 'name', 'is_deleted'];
}
