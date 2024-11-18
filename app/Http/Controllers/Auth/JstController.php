<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class JstController extends Controller
{
    public function generateAuthorizationUrl()
    {
        // Tham số được cung cấp bởi sàn
        $appKey = '4jhOwBqfD6GqCHrU';           // Thay bằng appkey thực tế
        $appSecret = 'Bg8goH0ey9UOclnf';        // Thay bằng appsecret thực tế
        $state = '';    // Tùy chọn (có thể để rỗng)

       
        $state = '';       // Giá trị tự định nghĩa, dùng để xác thực callback

        // Tạo timestamp
        $timestamp = time();

        // Tạo Sign (chữ ký)
        $rawString = "appkey={$appKey}&appsecret={$appSecret}&timestamp={$timestamp}&state={$state}";
        $sign = md5($rawString);
        // Tạo URL
         $authUrl = "https://global-erp.jushuitan.cn/account/companyauth/auth";
      
        $authorizationUrl = "{$authUrl}?appkey={$appKey}&timestamp={$timestamp}&sign={$sign}&state={$state}";

        return response()->json(['authorization_url' => $authorizationUrl]);
    }
    public function handleCallback(Request $request)
    {
        $code = $request->query('Code');  // Lấy Authorization Code từ query string
        $state = $request->query('state'); // Lấy state (nếu có)

        if (!$code) {
            return response()->json(['error' => 'Không nhận được Authorization Code'], 400);
        }

        // Tiến hành lấy Access Token bằng Authorization Code
        return $this->getAccessToken($code);
    }


    public function getAccessToken($authorizationCode)
    {
        $appKey = '4jhOwBqfD6GqCHrU';           // Thay bằng appkey thực tế
        $appSecret = 'Bg8goH0ey9UOclnf';        // Thay bằng appsecret thực tế
        $redirectUri = 'https://shipdemo.thienlong.vn/jst/callback'; // Callback URL đã đăng ký

        // URL lấy Access Token từ sàn
        $tokenUrl = "https://global-erp.jushuitan.cn/oauth/token";

        // Gửi POST yêu cầu
        $response = Http::asForm()->post($tokenUrl, [
            'appkey' => $appKey,
            'appsecret' => $appSecret,
            'code' => $authorizationCode,
            'redirect_uri' => $redirectUri,
        ]);

        // Kiểm tra phản hồi từ API
        if ($response->failed()) {
            return response()->json([
                'error' => 'Không lấy được Access Token',
                'details' => $response->json(),
            ], $response->status());
        }

        // Trả về Access Token
        $data = $response->json();
        return response()->json([
            'access_token' => $data['access_token'] ?? null,
            'expires_in' => $data['expires_in'] ?? null,
        ]);
    }
}
