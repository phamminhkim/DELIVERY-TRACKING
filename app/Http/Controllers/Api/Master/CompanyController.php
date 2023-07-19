<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class CompanyController extends ResponseController
{
    public function getAvailableCompanies(Request $request)
    {

        $handler = MasterRepository::companyRequest($request);
        $companies = $handler->getAvailableCompanies();

        if ($companies) {
            return $this->responseSuccess($companies);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add
    public function createNewCompany(Request $request)
    {

        $handler = MasterRepository::companyRequest($request);
        $commpany = $handler->createNewCompany();

        if ($commpany) {
            return $this->responseSuccess($commpany);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingCompany(Request $request, $code)
    {
        $handler = MasterRepository::companyRequest($request);
        $company = $handler->updateExistingCompany($code);

        if ($company) {
            return $this->responseSuccess($company);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingCompany(Request $request, $code)
    {
        $handler = MasterRepository::companyRequest($request);
        $is_success = $handler->deleteExistingCompany($code);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
