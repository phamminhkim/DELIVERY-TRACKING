<?php

namespace App\Repositories\Admin;

use App\Models\System\Role;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Utilities\RedisUtility;
use Illuminate\Support\Facades\Validator;

class RoleRepository extends RepositoryAbs
{
    public function getAvailableRoles()
    {
        try {
            $query = Role::query();
            $roles = $query->get();
            $result = array();

            if ($this->request->filled('format')) {
                if ($this->request->format == 'treeselect') {
                    foreach ($roles as $role) {
                        $item = array(
                            'id' => $role->id,
                            'label' => $role->name . ' (' . $role->guard_name . ')',
                            'object' => $role
                        );
                        array_push($result, $item);
                    }
                }
            } else {
                $result = $roles;
            }
            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function createNewRole()
    {
        try {
            $validator = Validator::make($this->data, [
                'name' => 'required|string|unique:roles,name,NULL,id,guard_name,' . $this->data['guard_name'],
                'guard_name' => 'required|string',
            ], [
                'name.required' => 'Tên role là bắt buộc.',
                'name.string' => 'Tên role phải là một chuỗi.',
                'name.unique' => 'Tên và guard đã tồn tại.',
                'guard_name.required' => 'Guard là bắt buộc.',
                'guard_name.string' => 'Guard phải là một chuỗi.',
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
                'name' => 'required|string',
                'guard_name' => 'required|string',
            ], [
                'name.required' => 'Tên role là bắt buộc.',
                'name.string' => 'Tên role phải là một chuỗi.',
                'guard_name.required' => 'Guard là bắt buộc.',
                'guard_name.string' => 'Guard phải là một chuỗi.',
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
            RedisUtility::deleteByCategory('menu-tree');
            return true;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
}
