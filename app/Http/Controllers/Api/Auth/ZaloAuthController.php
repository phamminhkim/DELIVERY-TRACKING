<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController\ResponseController;
use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Services\SocialAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;

class ZaloAuthController extends ResponseController
{
    /**
     * Kiểm tra tài khoản user zalo đã được đăng ký chưa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkExistingUser(Request $request)
    {
        $provider_name = 'ZaloProvider';
        $provider_id = $request->zalo_user_id;
        $account = SocialAccount::whereProvider($provider_name)
            ->whereProviderUserId($provider_id)
            ->first();

        if ($account && $account->user) {
            return $this->responseSuccess([], 'User is exist');
        } else {
            return $this->responseError('User is not exist');
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
        $zalo_access_token = $request->access_token;
        $zalo_user_phone = $request->phone;
        $config = array(
            'app_id' => config("services.zalo.client_id"),
            'app_secret' =>  config("services.zalo.client_secret")
        );
        $zalo = new Zalo($config);

        $params = ['fields' => 'id,name,picture'];
        $response = $zalo->get(ZaloEndPoint::API_GRAPH_ME, $zalo_access_token, $params);
        $result = $response->getDecodedBody(); // result
        //dd($result);//check ở đây
        if (isset($result) && isset($result['id'])) {
            $service = new SocialAccountService;
            $result['phone_number'] = $zalo_user_phone;
            $user = $service->createOrGetUserFromZalo($result);

            Auth::login($user);
            $user_login = Auth::user();
            $access_token = $user_login->createToken('authToken')->accessToken;
            $res['access_token'] = $access_token;
            if ($user) {
                if ($user->active == 1) {
                    return $this->responseSuccess($res, 'Logged in successfully');
                } else {
                    return $this->responseError('User is not active');
                }
            } else {
                return $this->responseError('Login failed');
            }
        }else{
            return $this->responseError('Login failed');
        }
    }
}
