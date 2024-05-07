<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\BaseController\ResponseController;
use Illuminate\Http\Request;
use App\Repositories\MasterRepository;

class UserController extends ResponseController
{
    public function getAvailableUsers(Request $request)
    {

        $handler = MasterRepository::userRequest($request);
        $users = $handler->getAvailableUsers();

        if ($users) {
            return $this->responseSuccess($users);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    //add
    public function createNewUser(Request $request)
    {

        $handler = MasterRepository::userRequest($request);
        $user = $handler->createNewUser();

        if ($user) {
            return $this->responseSuccess($user);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    //update
    public function updateExistingUser(Request $request, $id)
    {
        $handler = MasterRepository::userRequest($request);
        $user = $handler->updateExistingUser($id);

        if ($user) {
            return $this->responseSuccess($user);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function deleteExistingUser(Request $request, $id)
    {
        $handler = MasterRepository::UserRequest($request);
        $is_success = $handler->deleteExistingUser($id);

        if ($is_success) {
            return $this->responseOk('success');
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
    public function expandLeftMenuUser(Request $request)
    {
        
        $handler = MasterRepository::UserRequest($request);
        $is_success = $handler->expandLeftMenu();

        if ($is_success) {
            return $this->responseOk('success');
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
