<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class EmployeeController extends ResponseController
{
    public function getAvailableEmployees(Request $request)
    {

        $handler = MasterRepository::employeeRequest($request);
        $employees = $handler->getAvailableEmployees();

        if ($employees) {
            return $this->responseSuccess($employees);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add 
    public function createNewEmployee(Request $request)
    {

        $handler = MasterRepository::employeeRequest($request);
        $employee = $handler->createNewEmployee();

        if ($employee) {
            return $this->responseSuccess($employee);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingEmployee(Request $request, $id)
    {
        $handler = MasterRepository::employeeRequest($request);
        $employee = $handler->updateExistingEmployee($id);

        if ($employee) {
            return $this->responseSuccess($employee);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingEmployee(Request $request, $id)
    {
        $handler = MasterRepository::employeeRequest($request);
        $is_success = $handler->deleteExistingEmployee($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
