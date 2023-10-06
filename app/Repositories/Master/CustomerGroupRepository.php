<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerGroup;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class CustomerGroupRepository extends RepositoryAbs
{
    public function getAllCustomerGroups()
    {
        return CustomerGroup::all();
    }
}
