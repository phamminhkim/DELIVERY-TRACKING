<?php

namespace App\Repositories\System;

use App\Models\System\ServiceToken;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\SocialAccountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Zalo\Builder\MessageBuilder;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;
use Wilkques\PKCE\Generator;

class ZaloRepository extends RepositoryAbs
{
    private static function getInstance()
    {
        $config = array(
            'app_id' => config("services.zalo.client_id"),
            'app_secret' =>  config("services.zalo.client_secret")
        );

        return new Zalo($config);
    }
    private static function getOaAccessToken()
    {
        $existing_token = ServiceToken::where('provider', 'zalo')->where('category', 'oa_access_token')->first();
        if ($existing_token) {
            if ($existing_token->expired_at > now()) {
                return $existing_token->access_token;
            } else {
                Log::info("ZaloOA access token has expired at "  . $existing_token->expired_at->format('Y-m-d H:i:s'));
                $response = Http::asForm()->withHeaders([
                    'secret_key' => config("services.zalo.client_secret"),
                ])->post('https://oauth.zaloapp.com/v4/oa/access_token', [
                    'app_id' => config("services.zalo.client_id"),
                    'refresh_token' => $existing_token->refresh_token,
                    'grant_type' => 'refresh_token'
                ]);

                $result = $response->json();
                if (isset($result['access_token'])) {
                    $expired_at = $existing_token->expired_at->addSeconds($result['expires_in']);
                    ZaloRepository::updateOaAccessToken($result['access_token'], $result['refresh_token'], $expired_at);
                    return $result['access_token'];
                } else {
                    Log::info("Failed to request ZaloOA access token");
                    throw new \Exception("OA access token not found");
                }
            }
        } else {
            Log::info("Request ZaloOA access token not found");
            throw new \Exception("OA access token not found");
        }
    }
    private static function getJourneyToken($phone_number)
    {
        $access_token = ZaloRepository::getOaAccessToken();
        $response = Http::withHeaders([
            'access_token' => $access_token,
        ])->post('https://business.openapi.zalo.me/journey/get-token', [
            'phone' => $phone_number,
            'token_type' => 'token_logistics_30',
        ]);
        $result = $response->json();
        if (isset($result['token'])) {
            return $result['token'];
        } else {
            Log::error("Failed to request Journey token for " . $phone_number);
            Log::error($result);
            throw new \Exception("Journey token for " . $phone_number . " not found");
        }
    }
    private static function updateOaAccessToken($access_token, $refresh_token, $expired_at)
    {
        try {
            $existing_token = ServiceToken::where('provider', 'zalo')->where('category', 'oa_access_token')->first();
            if ($existing_token) {
                $existing_token->access_token = $access_token;
                $existing_token->refresh_token = $refresh_token;
                $existing_token->expired_at =  $expired_at->format('Y-m-d H:i:s');
                $existing_token->save();
                Log::info("Import ZaloOA access token, expired at " . $expired_at->format('Y-m-d H:i:s'));
            } else {
                $existing_token = ServiceToken::create([
                    'provider' => 'zalo',
                    'category' => 'oa_access_token',
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                    'expired_at' =>  $expired_at->format('Y-m-d H:i:s')
                ]);
                Log::info("Update ZaloOA access token, expired at " . $expired_at->format('Y-m-d H:i:s'));
            }
            return $existing_token;
        } catch (\Exception $e) {
            Log::info("Failed to update ZaloOA access token");
            throw new \Exception("Failed to update OA access token");
        }
    }

    public function UserAuthorizeUrl()
    {
        $zalo = $this->getInstance();
        $helper = $zalo->getRedirectLoginHelper();
        $code_verifier = Generator::codeVerifier();
        session()->put('codeVerifier', $code_verifier);

        $code_challenge = Generator::codeChallenge($code_verifier);
        $state = uniqid();
        $callback_url =   config("services.zalo.redirect");
        $authorize_url = $helper->getLoginUrl($callback_url, $code_challenge, $state);

        return $authorize_url;
    }

    public function UserCallback()
    {
        $zalo = $this->getInstance();
        $helper = $zalo->getRedirectLoginHelper();
        $code_verifier = session()->get('codeVerifier');

        $zalo_token = $helper->getZaloToken($code_verifier); // get zalo token
        $user_access_token = $zalo_token->getAccessToken();

        $user_info = $this->getUserInfo('id,name,picture', $user_access_token);

        $service = new SocialAccountService;
        $user = $service->createOrGetUserFromZalo($user_info);

        Auth::login($user);

        return true;
    }

    public function OaAuthorizeUrl()
    {
        $zalo = $this->getInstance();
        $helper = $zalo->getRedirectLoginHelper();
        $code_verifier = Generator::codeVerifier();
        session()->put('codeVerifier', $code_verifier);

        $code_challenge = Generator::codeChallenge($code_verifier);
        $state = uniqid();
        $callback_url = config("services.zalo.redirect_oa"); //"https://shipdemo.thienlong.vn/auth/zalo/callback";
        $authorize_url = $helper->getLoginUrlByOA($callback_url, $code_challenge, $state);

        return $authorize_url;
    }

    public function OaCallback()
    {
        $zalo = $this->getInstance();
        $helper = $zalo->getRedirectLoginHelper();
        $code_verifier = session()->get('codeVerifier');

        $zalo_token = $helper->getZaloTokenByOA($code_verifier); // get zalo token
        $oa_access_token = $zalo_token->getAccessToken();

        $this->updateOaAccessToken($oa_access_token, $zalo_token->getRefreshToken(), $zalo_token->getAccessTokenExpiresAt());

        $oa_info = $this->getUserInfo('id,name,picture', $oa_access_token);
        $this->sendOaMessage($oa_info['id'], "Đã cấp OA access token thành công cho ứng dụng", $oa_access_token);

        return true;
    }

    public function getUserInfo($fields, $access_token)
    {
        $zalo = $this->getInstance();

        $params = ['fields' => $fields];
        $response = $zalo->get(ZaloEndPoint::API_GRAPH_ME, $access_token, $params);

        return $response->getDecodedBody();
    }
    //Hàm này gửi tin nhắn khi user là 
    public function sendOaMessage($user_id, $message)
    {
        $zalo = $this->getInstance();
        $oa_access_token = $this->getOaAccessToken();

        $msg_builder = new MessageBuilder(MessageBuilder::MSG_TYPE_TXT);
        $msg_builder->withUserId($user_id);
        $msg_builder->withText($message);
        $msg_text = $msg_builder->build();

        $response = $zalo->post(ZaloEndPoint::API_OA_SEND_CONSULTATION_MESSAGE_V3, $oa_access_token, $msg_text);
        return $response->getDecodedBody();
    }
    public static function sendZaloSmsWithTemplate($phone, $template_id, $template_data)
    {
        $oa_access_token = ZaloRepository::getOaAccessToken();
        $journey_token = ZaloRepository::getJourneyToken($phone);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_token' => $oa_access_token,
            'journey_token' => $journey_token,
        ])->post('https://business.openapi.zalo.me/message/template', [
            'phone' => $phone,
            'template_id' => $template_id,
            'template_data' => $template_data,
            'tracking_id' => 'tracking_id'
        ]);

        return $response;
    }
}
