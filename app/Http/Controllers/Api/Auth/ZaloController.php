<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController\ResponeseController;
use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use Illuminate\Http\Request;

class ZaloController extends ResponeseController
{

    
    /**
     * Kiểm tra tài khoản user zalo đã được đăng ký chưa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function existUser(Request $request)
    {
        $providerName = 'ZaloProvider';
        $providerId = $request->zalo_user_id;
        $account = SocialAccount::whereProvider($providerName)
        ->whereProviderUserId($providerId)
        ->first();
       // dd($providerId);
        if($account && $account->user){
            return $this->sendSuccess('user is exist');
        }else{
            return $this->sendFailedWithMessage('user is not exist');
        }
    }
         /**
     * Login vào hệ thống.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        dd("login");
    }
}
