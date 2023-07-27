<?php

namespace App\Repositories\System;

use App\Models\System\ServiceToken;
use App\Repositories\Abstracts\RepositoryAbs;
use App\Services\SocialAccountService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Zalo\Builder\MessageBuilder;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;
use Wilkques\PKCE\Generator;
use Illuminate\Http\Request;

class OneTLRepository extends RepositoryAbs
{

    protected $client_id;
    protected $client_secret;
    protected $url;
    protected $redirect;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->client_id = config("services.onetl.client_id");
        $this->client_secret =  config("services.onetl.client_secret");
        $this->url =  config("services.onetl.url");
        $this->redirect =  config("services.onetl.redirect");
    }
    public function UserAuthorizeUrl()
    {

        $query = http_build_query([
            'client_id' =>  $this->client_id,
            'redirect_uri' =>  $this->redirect, //'http://localhost:8200/auth/onetl/callback',
            'response_type' => 'code',
            'scope' => ''
        ]);
        $authorize_url =  $this->url . '/oauth/authorize?' . $query;

        return $authorize_url;
    }

    public function UserCallback()
    {

        $http = new \GuzzleHttp\Client;
        $body = '';
        //dd($request->code);
        if ($this->request->code) {
            //$response = $http->post('http://onethienlong.local:80/oauth/token', [
            $response = $http->post($this->url . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'redirect_uri' =>  $this->redirect,
                    'code' => $this->request->code
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            $response = $http->get($this->url . '/api/user/myinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $body['access_token'],
                ],
            ]);
            $res = json_decode((string) $response->getBody(), true);
            $data = $res['data'];
            //dd($data['avatar']);
            $data['avatar'] = $this->url .  $data['avatar'];
            $service = new SocialAccountService;
            $user = $service->createOrGetUserFromOnetl($data);
            Auth::login($user);
            return true;
        }
       
    }
}
