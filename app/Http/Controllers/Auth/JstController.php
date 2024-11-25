<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\Sap\SapApiHelper;
use App\Http\Controllers\Controller;
use App\JsOauthToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class JstController extends Controller
{
    public function generateAuthorizationUrl()
    {
        // Tham số được cung cấp bởi sàn
        $appKey = config('api_site.connections.jst.app_key'); // '4jhOwBqfD6GqCHrU';           // Thay bằng appkey thực tế
        $appSecret = config('api_site.connections.jst.app_secret'); //'Bg8goH0ey9UOclnf';        // Thay bằng appsecret thực tế
        $state = 'SAAS';    // Tùy chọn (có thể để rỗng)
        // Tạo timestamp
        // date_default_timezone_set('UTC');
        $timestamp = time();
        // Tạo Sign (chữ ký)
        $str = [];
        $str[] = "appkey=" . $appKey;
        $str[] = "appsecret=" . $appSecret;
        $str[] = "state=" . $state;
        $str[] = "timestamp=" . $timestamp;
        $signStr = implode("&", $str);
        $sign =  md5($signStr);
        // "https://global-erp.jushuitan.cn/account/companyauth/auth?" là địa chỉ trong env JST_APP_ADDRESS_AUTH_URL

        $url = config('api_site.connections.jst.app_address_auth') // "https://global-erp.jushuitan.cn/account/companyauth/auth?"
            . "?"
            . "appkey=" . $appKey
            . "&timestamp=" . $timestamp
            . "&sign=" . $sign
            . "&state=" . $state;
        return  redirect($url);
    }
    public function handleCallback(Request $request)
    {
        $code = $request->query('code');
        $state = $request->query('state');
        if (  $code && $state) {
            $this->sendCodeToSAP($code,$state);
        }
        // if (!$code) {
        //     return response()->json(['error' => 'Không nhận được Authorization Code'], 400);
        // }
        // // Tiến hành lấy Access Token bằng Authorization Code
        // return $this->getAccessToken($code);
    }


    public function getAccessToken($authorizationCode)
    {
        $appKey = config('api_site.connections.jst.app_key'); // '4jhOwBqfD6GqCHrU';           // Thay bằng appkey thực tế
        $appSecret = config('api_site.connections.jst.app_secret'); //'Bg8goH0ey9UOclnf';        // Thay bằng appsecret thực tế
        $redirectUri = config('api_site.connections.jst.app_callback_url'); // 'https://shipdemo.thienlong.vn/jst/callback/'; // Callback URL đã đăng ký
        $state = 'SAAS';
        $timestamp = time();
        $str = [];
        $str[] = "appkey=" . $appKey;
        $str[] = "appsecret=" . $appSecret;
        $str[] = "data=" . json_encode(array('code' => $authorizationCode));
        $str[] = "ts=" . $timestamp;
        $signStr = implode("&", $str);
        $sign = md5($signStr);
        $url = config('api_site.connections.jst.app_openapi_url') . '/api/Authentication/GetToken';
        $response = Http::withHeaders([
            'appkey' => $appKey,
            'appsecret' => $appSecret,
            'ts' => $timestamp,
            'sign' => $sign,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'code' => $authorizationCode,
        ]);
        if ($response->successful()) {
            $data = $response->json();
            $this->saveAccessToken($data);
            return response()->json([
                'success' => true,
                'data' => $response->json(),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $response->body(),
            ], $response->status());
        }
    }
    public function saveAccessToken($data)
    {
        $expiredTime = Carbon::parse($data['data']['refreshTokenExpireTime'])->format('Y-m-d H:i:s');
        $refreshTokenExpireTime = Carbon::parse($data['data']['refreshTokenExpireTime'])->format('Y-m-d H:i:s');
        $token = JsOauthToken::create([
            'access_token' => $data['data']['accessToken'],
            'refresh_token' => $data['data']['refreshToken'],
            'expired_time' => $expiredTime,
            'refresh_token_expire_time' => $refreshTokenExpireTime,
            'company_id' => $data['data']['companyId'],
        ]);
        // $this->callSapApi($token->access_token);
    }
    //refreshToken
    public function refreshToken($refreshToken)
    {
        $appKey = config('api_site.connections.jst.app_key'); // '4jhOwBqfD6GqCHrU';           // Thay bằng appkey thực tế
        $appSecret = config('api_site.connections.jst.app_secret'); //'Bg8goH0ey9UOclnf';        // Thay bằng appsecret thực tế
        $redirectUri = config('api_site.connections.jst.app_callback_url'); // 'https://shipdemo.thienlong.vn/jst/callback/'; // Callback URL đã đăng ký
        $state = 'SAAS';
        $timestamp = time();
        $str = [];
        $str[] = "appkey=" . $appKey;
        $str[] = "appsecret=" . $appSecret;
        $str[] = "refresh_token=" . $refreshToken;
        $str[] = "ts=" . $timestamp;
        $signStr = implode("&", $str);
        $sign = md5($signStr);
        $url = config('api_site.connections.jst.app_openapi_url') .  '/api/Authentication/RefreshToken';
        $response = Http::withHeaders([
            'appkey' => $appKey,
            'appsecret' => $appSecret,
            'ts' => $timestamp,
            'sign' => $sign,
            'Content-Type' => 'application/json',
        ])->post($url);
        if ($response->successful()) {
            $this->saveAccessToken($response->json());
            return response()->json([
                'success' => true,
                'data' => $response->json(),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $response->body(),
            ], $response->status());
        }
    }
    public function sendCodeToSAP($code,$state)
    {
        try {
            $sapData = [
                "ID" => "1004",
                "action_name" => "GET_AUTH_CODE",
                "BODY" => [
                    [
                        "code" => $code,
                        "state" => $state,
                    ]
                ]
            ];

            $json = SapApiHelper::postData(json_encode($sapData));
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            //throw $th;
        }
    }
    public function callSapApi($accessToken)
    {
        $sapUrl = config('api_site.connections.sap.endpoint'); // Đường dẫn API SAP
        $response = Http::post($sapUrl, [
            'access_token' => $accessToken,
        ]);

        if ($response->failed()) {
            throw new \Exception('Không thể gọi API SAP: ' . $response->body());
        }
    }
}
