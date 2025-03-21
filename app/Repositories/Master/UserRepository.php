<?php

namespace App\Repositories\Master;

use App\User;
use App\Models\System\Role;
use App\Utilities\MenuUtility;
use App\Utilities\RedisUtility;
use App\Models\Shared\ConfigUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Abstracts\RepositoryAbs;

class UserRepository extends RepositoryAbs
{
    public function getAvailableRoleUsers()
    {
        try {
            // user-processing-book
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'user-processing-book');
            })->get();
            $result = array();

            if ($this->request->filled('format')) {
                if ($this->request->format == 'treeselect') {
                    foreach ($users as $user) {
                        $item = array(
                            'id' => $user->id,
                            'label' => $user->name,
                            'object' => $user
                        );
                        array_push($result, $item);
                    }
                }
            } else {
                $result = $users;
            }
            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getAvailableUsers()
    {
        try {
            $users = User::with(['roles', 'social_accounts', 'delivery_partners'])->get()->map(function ($user) {
                $user->role_ids = $user->roles->pluck('id')->toArray();
                return $user;
            });
            $result = array();

            if ($this->request->filled('format')) {
                if ($this->request->format == 'treeselect') {
                    foreach ($users as $user) {
                        $item = array(
                            'id' => $user->id,
                            'label' => $user->name,
                            'object' => $user
                        );
                        array_push($result, $item);
                    }
                }
            } else {
                $result = $users;
            }
            return $result;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }
    public function getUsersByRole()
    {
        try {
            // Retrieve users with specific roles
            $users = User::with(['roles'])
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['user-sale', 'admin-ConvertPO']);
                })
                ->get()
                ->map(function ($user) {
                    $user->role_ids = $user->roles->pluck('id')->toArray(); // Collect role IDs
                    return $user; // Return modified user object
                });

            return $users; // Return the filtered user list
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
        }
    }

    public function changePassword()
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'email' => 'required|string',
                'current_password' => 'required|string',
                'new_password' => 'required|string',
                'password_confirm' => 'required|string',
            ], [
                'email.required' => 'Email là bắt buộc.',
                'email.string' => 'Email phải là chuỗi.',
                'current_password.required' => 'Mật khẩu hiện tại là bắt buộc',
                'new_password.required' => 'Mật khẩu mới là bắt buộc',
                'password_confirm.required' => 'Nhập lại mật khẩu mới là bắt buộc',
            ]);

            if ($validator->fails()) {
                $this->errors = $validator->errors()->toArray();
                return false;
            }

            // Lấy thông tin người dùng đang đăng nhập
            $user = User::where('email', $this->request->input('email'))->first();

            if (!$user) {
                $this->errors['email'] = 'Người dùng không tồn tại.';
                return false;
            }

            // Kiểm tra mật khẩu hiện tại
            $currentPassword = $this->request->input('current_password');

            if (!Hash::check($currentPassword, $user->password)) {
                $this->errors['current_password'] = 'Mật khẩu hiện tại không chính xác.';
                return false;
            }

            // Kiểm tra mật khẩu mới và nhập lại mật khẩu mới
            $newPassword = $this->request->input('new_password');
            $passwordConfirm = $this->request->input('password_confirm');

            if ($newPassword !== $passwordConfirm) {
                $this->errors['password_confirm'] = 'Mật khẩu mới không khớp.';
                $this->errors['new_password'] = 'Mật khẩu mới không khớp.';
                return false;
            }

            // Cập nhật mật khẩu mới cho người dùng
            $user->password = Hash::make($newPassword);
            $user->save();

            // Trả về thông báo thành công
            return $user;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            $this->errors = $exception->getTrace();
            return false;
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
    public function expandLeftMenu()
    {


        $validator = Validator::make($this->request->all(), [
            'code' => 'required',
            'value' => 'required',
        ]);
        $failed = $validator->fails();
        if ($failed) {
            $this->errors =  $validator->errors();
            // $result['data']['errors']  = $validator->errors();
        } else {
            $code = $this->request->code;
            switch ($code) {
                case MenuUtility::$EXPAND_LEFT_MENU:

                    $expand = ConfigUser::where('user_id', auth()->user()->id)
                        ->where('code', 'expand_menu')->first();

                    if ($expand == null) {
                        $expand = new ConfigUser;
                        //dd("test");
                        $expand->code = MenuUtility::$EXPAND_LEFT_MENU;
                        $expand->value =  $this->request->value;
                        $expand->user_id = auth()->user()->id;

                        $expand->save();
                    } else {

                        $expand->value = $this->request->value;
                        $expand->save();
                    }
                    break;
            }
            return $expand;
            // $result['data']['success']  = 1;
            // $result['data']['config']  = $expand;
        }
        return null;

        //  return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }
}
