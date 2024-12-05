<?php

namespace App\Repositories\Master;

use App\CustomerPartnerStore;
use App\Models\Master\Company;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookStoreRepository extends RepositoryAbs
{
    public function getAllBookStore()
    {
        try {
            $query = CustomerPartnerStore::query();
            $customer_partner_store = $query->get();

            return $customer_partner_store;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

}
