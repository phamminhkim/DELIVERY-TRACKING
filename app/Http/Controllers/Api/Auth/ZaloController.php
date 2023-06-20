<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController\ResponeseController;
use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Services\SocialAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;

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
        $accessTokenZalo = $request->access_token;
        $config = array(
            'app_id' => config("services.zalo.client_id") ,
            'app_secret' =>  config("services.zalo.client_secret")
        );

        $zalo = new Zalo($config);
    

        $helper = $zalo->getRedirectLoginHelper();

        $params = ['fields' => 'id,name,picture'];
        $response = $zalo->get(ZaloEndPoint::API_GRAPH_ME, $accessTokenZalo, $params);
        $result = $response->getDecodedBody(); // result
        $service = new SocialAccountService;
        $user = $service->createOrGetUserFromZalo($result);
        
        Auth::login($user);
        $userLogin = Auth::user();
        $accessToken = $userLogin->createToken('authToken')->accessToken;
        $res['access_token'] =$accessToken;
        if($user  ){
           if($user->active == 1){
            return $this->sendResponse( $res,'Logged in successfully');
           }else{
            return $this->sendFailedWithMessage( 'User is locked');
           }
        }else{
            return $this->sendFailedWithMessage('Login is fault');
        }
    }
}
