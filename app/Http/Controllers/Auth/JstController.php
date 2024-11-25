<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\Sap\SapApiHelper;
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
}
