<?php

namespace App\Repositories\Master;

use App\Models\Master\Company;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CompanyRepository extends RepositoryAbs
{
    public function getAvailableCompanies()
    {
        try {
            $companies = DB::table('companies')->get();
            return $companies;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewCompany()
    {
        try {
            $validator = Validator::make($this->data, [
                'code' => 'required|string|unique:companies,code',
                'name' => 'required|string',
            ], [
                'code.required' => 'Yêu cầu nhập mã công ty.',
                'code.string' => 'Mã công ty phải là chuỗi.',
                'code.unique' => 'Mã công ty đã tồn tại.',
                'name.required' => 'Yêu cầu nhập tên công ty.',
                'name.string' => 'Tên công ty phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $company => $validator) {
                    if ($errors->has($company)) {
                        $this->errors[$company] = $errors->first($company);
                        return false;
                    }
                }
            } else {
                $this->data['is_active'] = true;
                $company = Company::create($this->data);
                return $company;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }


    public function updateExistingCompany($code)
    {
        try {
            $company = Company::where('code', $code)->firstOrFail();

            $validator = Validator::make($this->data, [
                'name' => 'required|string',
            ], [
                'name.required' => 'Yêu cầu nhập tên công ty.',
                'name.string' => 'Tên công ty phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $company => $validator) {
                    if ($errors->has($company)) {
                        $this->errors[$company] = $errors->first($company);
                        return false;
                    }
                }
            } else {
                $company = Company::findOrFail($code);
                $company->update($this->data);
                return $company;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingCompany($code)
    {
        try {
            $company = Company::findOrFail($code);
            $company->delete();
            return $company;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
