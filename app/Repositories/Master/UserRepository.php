<?php

namespace App\Repositories\Master;

use App\User;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;

class UserRepository extends RepositoryAbs
{
    public function getAvailableUsers()
    {
        try {
            $users = User::all();
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
                'email' => 'nullable|string|unique:users,email',
                'phone_number' => 'nullable|string',
            ], [

                'name.required' => 'Yêu cầu nhập tên User.',
                'name.string' => 'Tên User phải là chuỗi.',
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
                'phone_number' => 'nullable|string',
            ], [

                'name.required' => 'Yêu cầu nhập tên User.',
                'name.string' => 'Tên User phải là chuỗi.',
                'email.string' => 'Email phải là chuỗi.',
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
                $user->update($this->data);

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
