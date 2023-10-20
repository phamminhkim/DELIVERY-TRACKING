<?php

namespace App\Repositories\Master;

use App\Models\Master\CustomerGroup;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class CustomerGroupRepository extends RepositoryAbs
{
    public function getAllCustomerGroups()
    {
        $query = CustomerGroup::query();
        $query
            ->with(['extract_order_configs' => function ($query) {
                $query->where('is_official', true);
            }])
            ->with(['customers' => function ($query) {
                $query->pluck('customer_id');
            }]);

        return $query->get();
    }
}
