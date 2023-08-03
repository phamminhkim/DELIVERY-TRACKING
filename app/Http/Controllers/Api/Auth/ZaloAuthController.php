<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController\ResponseController;
use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Services\SocialAccountService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;

class ZaloAuthController extends ResponseController
{
    public function __construct()
    {

        $this->middleware('auth:api')->except(['checkExistUser', 'verifyUserPhone', 'login','processWebHook']);
    }
    /**
     * Kiểm tra tài khoản user zalo đã được đăng ký chưa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkExistUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zalo_user_id' => 'required'
        ], [
            'zalo_user_id.required' => 'Input zalo user id'
        ]);
        if ($validator->fails()) {
            return $this->responseError($validator->errors());
        } else {
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
    }
    /**
     * Kiểm tra tài khoản user zalo đã được đăng ký số điện thoại chưa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyUserPhone(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'zalo_user_id' => 'required'
        ], [
            'zalo_user_id.required' => 'Input zalo user id'
        ]);
        if ($validator->fails()) {
            return $this->responseError($validator->errors());
        } else {
            $provider_name = 'ZaloProvider';
            $provider_id = $request->zalo_user_id;
            $account = SocialAccount::whereProvider($provider_name)
                ->whereProviderUserId($provider_id)
                ->first();

            if ($account && $account->user) {
                if ($account->user->phone_number != '') {

                    return $this->responseSuccess([], 'User already exists and has phone number verified');
                } else {
                    return $this->responseError('User already exists but has not verified phone number', [], 204);
                }
            } else {
                return $this->responseError('User is not exist');
            }
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
        $validator = Validator::make($request->all(), [
            'access_token' => 'required'
        ], [
            'access_token.required' => 'Input zalo access_token'
        ]);
        if ($validator->fails()) {
            return $this->responseError($validator->errors());
        } else {
            $zalo_access_token = $request->access_token;
            // $zalo_user_phone = $request->phone_number;
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
                // $result['phone_number'] = $zalo_user_phone;
                $user = $service->createOrGetUserFromZalo($result);

                Auth::login($user);
                $user_login = Auth::user();
                $access_token = $user_login->createToken('authToken')->accessToken;
                $res['phone_number'] = $user->phone_number;
                $res['access_token'] = $access_token;
              
                if ($user) {
                    return $this->responseSuccess($res, 'Logged in successfully');
                    // if ($user->active == 1) {
                    //     return $this->responseSuccess($res, 'Logged in successfully');
                    // } else {
                    //     return $this->responseError('User is not active');
                    // }
                } else {
                    return $this->responseError('Login failed');
                }
            } else {
                return $this->responseError('Login failed');
            }
        }
    }
    public function updatePhoneNumber(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required'
        ], [
            'phone_number.required' => 'Input phone number'
        ]);
        if ($validator->fails()) {
            return $this->responseError($validator->errors());
        } else {

            $user = User::find(Auth()->user()->id);
            $user->phone_number = $request->phone_number;
            $user->save();
            return $this->responseSuccess('Updated in successfully');
        }
    }
    /**
     * Webhook
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processWebHook(Request $request)
    {
        $data = [
            "app_id" => $request->app_id,
            "user_id_by_app"  => $request->user_id_by_app,
            "event_name"  => $request->event_name,
            "timestamp"  => $request->timestamp,
            "sender"  =>  $request->sender,
            "recipient"  => $request->recipient,
            "message" => $request->message
        ] ;
        //Log::info("webhook:".  $data);
        //Hàm này xử lý dữ liệu không quá 2 giây. Nếu quá 2 giây Zalo sẽ báo webhook này không hoạt động.
        return $this->responseOk();
        
    }
}
