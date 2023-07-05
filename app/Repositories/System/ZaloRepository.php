<?php

namespace App\Repositories\System;

use App\Repositories\Abstracts\RepositoryAbs;
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
        $zalo = $this->getInstance();
        //$oa_access_token = $zalo->getOaAccessToken();
        //return $oa_access_token;
    }

    public function OaCallback()
    {
        $zalo = $this->getInstance();

        $helper = $zalo->getRedirectLoginHelper();
        $code_verifier = session()->get('codeVerifier');

        $zalo_token = $helper->getZaloTokenByOA($code_verifier); // get zalo token

        $oa_access_token = $zalo_token->getAccessToken();
        // dd($access_token);//Yêu cầu mã truy cập mức OA
        //Lưu access token vào DB
        $params = ['fields' => 'id,name,picture'];
        $response = $zalo->get(ZaloEndPoint::API_GRAPH_ME, $oa_access_token, $params);
        $result = $response->getDecodedBody(); // result

        $send_message = $this->sendOaMessage($result['id'], 'Xin chào', $oa_access_token);
        dd($send_message);
    }

    public function sendOaMessage($user_id, $message, $oa_access_token)
    {
        $zalo = $this->getInstance();
        $msg_builder = new MessageBuilder(MessageBuilder::MSG_TYPE_TXT);
        $msg_builder->withUserId($user_id);
        $msg_builder->withText($message);
        $msg_text = $msg_builder->build();

        // send request
        $response = $zalo->post(ZaloEndPoint::API_OA_SEND_CONSULTATION_MESSAGE_V3, $oa_access_token, $msg_text);

        return $response->getDecodedBody();
    }
}
