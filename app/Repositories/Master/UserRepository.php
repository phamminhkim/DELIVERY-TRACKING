<?php

namespace App\Repositories\Master;

use App\Models\System\Role;
use App\User;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Utilities\RedisUtility;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserRepository extends RepositoryAbs
{
    public function getAvailableUsers()
    {
        try {
           $users = User::with('roles')->get()->map(function ($user) {
                $user->role_ids = $user->roles->pluck('id')->toArray();
                return $user;
            });

              
            return $users;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewUser()
    {
        try {
            $validator = Validator::make($this->data, [
                'name' => 'required|string',
                'password' => 'required|string',
                'email' => 'nullable|string|unique:users,email',
                'phone_number' => 'nullable|string',
            ], [

                'name.required' => 'Yêu cầu nhập tên User.',
                'name.string' => 'Tên User phải là chuỗi.',
                'password.required' => 'Mật khẩu là bắt buộc',
                'password.string' => 'Mật khẩu phải là chuỗi',
                'email.string' => 'Email phải là chuỗi.',
                'email.unique' => 'Email không được trùng',
                'phone_number.string' => 'Số điện thoại phải là chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $user => $validator) {
                    if ($errors->has($user)) {
                        $this->errors[$user] = $errors->first($user);
                        return false;
                    }
                }
            } else {
                $this->data['active'] = true;
                $this->data['password'] = Hash::make($this->data['password']);
                $user = User::create($this->data);
                return $user;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function updateExistingUser($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'name' => 'required|string',
                'email' => 'nullable|string',
                'password' => 'nullable|string',
                'phone_number' => 'nullable|string',
            ], [
                'name.required' => 'Yêu cầu nhập tên User.',
                'name.string' => 'Tên User phải là chuỗi.',
                'email.string' => 'Email phải là chuỗi.',
                'password.string' => 'Password phải là chuỗi',
                //'email.unique' =>'Email không được trùng',
                'phone_number.string' => 'Số điện thoại phải là chuỗi.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $user => $validator) {
                    if ($errors->has($user)) {
                        $this->errors[$user] = $errors->first($user);
                        return false;
                    }
                }
            } else {
                $user = User::findOrFail($id);
                if (isset($this->data['password'])) {
                    $this->data['password'] = Hash::make($this->data['password']);
                }
                $user->update($this->data);

                $roles = Role::whereIn('id', $this->data['role_ids'])->get();
                $user->syncRoles($roles);
                RedisUtility::deleteByCategoryAndKey('menu-tree', $user->id);
                return $user;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return $user;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
