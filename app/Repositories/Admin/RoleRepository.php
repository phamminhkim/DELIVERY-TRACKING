<?php

namespace App\Repositories\Admin;

use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleRepository extends RepositoryAbs
{
     public function getAvailableRoles()
    {
        try {
            $roles = Role::get();
            return $roles;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewRole()
    {
        try {
            $validator = Validator::make($this->data, [
                'name' =>'required|string|unique:roles,name,NULL,id,guard,' . $this->data['guard'],
                'guard' => 'required|string',
            ], [
                'name.required' => 'Tên role là bắt buộc.',
                'name.string' => 'Tên role phải là một chuỗi.',
                'name.unique' => 'Tên và guard đã tồn tại.',
                'guard.required' => 'Guard là bắt buộc.',
                'guard.string' => 'Guard phải là một chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $role => $validator) {
                    if ($errors->has($role)) {
                        $this->errors[$role] = $errors->first($role);
                        return false;
                    }
                }
            } else {
                $role = Role::create($this->data);
                return $role;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function updateExistingRole($id)
    {
        try {
            $validator = Validator::make($this->data, [
                'name' =>'required|string',
                'guard' => 'required|string',
            ], [
                'name.required' => 'Tên role là bắt buộc.',
                'name.string' => 'Tên role phải là một chuỗi.',
                'guard.required' => 'Guard là bắt buộc.',
                'guard.string' => 'Guard phải là một chuỗi.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($this->data as $role => $validator) {
                    if ($errors->has($role)) {
                        $this->errors[$role] = $errors->first($role);
                        return false;
                    }
                }
            } else {
                $role = Role::findOrFail($id);
                $role->update($this->data);
                return $role;
            }
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function deleteExistingRole($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            return true;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
