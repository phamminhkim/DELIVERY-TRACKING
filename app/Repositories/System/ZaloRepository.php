<?php

namespace App\Repositories\System;

use App\Models\System\ServiceToken;
use App\Repositories\Abstracts\RepositoryAbs;
use Illuminate\Support\Facades\Http;
use Zalo\Builder\MessageBuilder;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;

class ZaloRepository extends RepositoryAbs
{
    private function getInstance()
    {
        $config = array(
            'app_id' => config("services.zalo.client_id"),
            'app_secret' =>  config("services.zalo.client_secret")
        );

        return new Zalo($config);
    }
    private function getOaAccessToken()
    {
        $existing_token = ServiceToken::where('provider', 'zalo')->where('category', 'oa_access_token')->first();
        if ($existing_token) {
            if ($existing_token->expired_at > now()) {
                return $existing_token->access_token;
            } else {
                $response = Http::post('https://oauth.zaloapp.com/v4/access_token', [
                    'app_id' => config("services.zalo.client_id"),
                    'app_secret' => config("services.zalo.client_secret"),
                    'code' => $existing_token->refresh_token,
                    'grant_type' => 'refresh_token'
                ]);

                $result = $response->json();
                if (isset($result['access_token'])) {
                    $this->updateOaAccessToken($result['accessToken'], $result['refreshToken'], $result['accessTokenExpiresAt']);
                    return $result['access_token'];
                } else {
                    throw new \Exception("OA access token not found");
                }
            }
        } else {
            throw new \Exception("OA access token not found");
        }
    }

    private function updateOaAccessToken($access_token, $refresh_token, $expired_at)
    {
        $existing_token = ServiceToken::where('provider', 'zalo')->where('category', 'oa_access_token')->first();
        if ($existing_token) {
            $existing_token->access_token = $access_token;
            $existing_token->refresh_token = $refresh_token;
            $existing_token->expired_at = $expired_at;
            $existing_token->save();
        } else {
            $existing_token = ServiceToken::create([
                'provider' => 'zalo',
                'category' => 'oa_access_token',
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'expired_at' => $expired_at
            ]);
        }
        return $existing_token;
    }

    public function OaCallback()
    {
        $zalo = $this->getInstance();

        $helper = $zalo->getRedirectLoginHelper();
        $code_verifier = session()->get('codeVerifier');

        $zalo_token = $helper->getZaloTokenByOA($code_verifier); // get zalo token
        $oa_access_token = $zalo_token->getAccessToken();

        $this->updateOaAccessToken($oa_access_token, $zalo_token->getRefreshToken(), $zalo_token->getAccessTokenExpiresAt());

        $oa_info = $this->getUserInfo('id,name,picture');
        $this->sendOaMessage($oa_info['id'], "Đã cấp OA access token thành công cho ứng dụng", $oa_access_token);
    }

    public function getUserInfo($fields)
    {
        $zalo = $this->getInstance();
        $oa_access_token = $this->getOaAccessToken();

        $params = ['fields' => $fields];
        $response = $zalo->get(ZaloEndPoint::API_GRAPH_ME, $oa_access_token, $params);

        return $response->getDecodedBody();
    }

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
}
