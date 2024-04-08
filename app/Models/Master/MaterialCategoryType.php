<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MaterialCategoryType extends Model
{
    protected $table = 'material_category_types';
    protected $fillable = ['code', 'name', 'is_deleted'];
}
