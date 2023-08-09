<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\AdminRepository;

class RoleController extends ResponseController
{
    public function getAvailableRoles(Request $request)
    {
        $handler = AdminRepository::roleRequest($request);
        $roles = $handler->getAvailableRoles();

        if ($roles) {
            return $this->responseSuccess($roles);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function createNewRole(Request $request)
    {
        $handler = AdminRepository::roleRequest($request);
        $role = $handler->createNewRole();

        if ($role) {
            return $this->responseSuccess($role);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function updateExistingRole(Request $request, $id)
    {
        $handler = AdminRepository::roleRequest($request);
        $role = $handler->updateExistingRole($id);

        if ($role) {
            return $this->responseSuccess($role);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingRole(Request $request, $id)
    {
        $handler = AdminRepository::roleRequest($request);
        $is_success = $handler->deleteExistingRole($id);

        if ($is_success) {
            return $this->responseOk();
        } else {
            $message = $handler->getMessage();
            $errors = $handler->getErrors();
            return $this->responseError($message, $errors);
        }
    }
}
