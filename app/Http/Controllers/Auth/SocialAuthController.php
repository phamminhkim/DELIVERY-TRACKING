<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;
use Illuminate\Support\Str;
use Wilkques\PKCE\Generator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Zalo\Builder\MessageBuilder;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
    public function handleProviderCallback(SocialAccountService $service, $provider)
    {

        $user = $service->createOrGetUser(Socialite::driver($provider));

        Auth::login($user);

        return redirect()->to('/');
    }
    public function redirectToZalo()
    {


        $config = array(
            'app_id' => config("services.zalo.client_id"),
            'app_secret' =>  config("services.zalo.client_secret")
        );
        $zalo = new Zalo($config);
        $helper = $zalo->getRedirectLoginHelper();
        $callback_url =   config("services.zalo.redirect"); //"https://shipdemo.thienlong.vn/auth/zalo/callback";
        $code_verifier = Generator::codeVerifier();
        session()->put('codeVerifier', $code_verifier);
        $code_challenge = Generator::codeChallenge($code_verifier);
        $state = uniqid();
        $loginUrl = $helper->getLoginUrl($callback_url, $code_challenge, $state);

        return redirect($loginUrl);
    }
    
    public function handleZaloCallback(Request $request)
    {

        $config = array(
            'app_id' => config("services.zalo.client_id"),
            'app_secret' =>  config("services.zalo.client_secret")
        );

        $zalo = new Zalo($config);


        $helper = $zalo->getRedirectLoginHelper();
        $callback_url =   config("services.zalo.redirect"); // $ZALO_REDIRECT_URI ;
        $code_verifier = session()->get('codeVerifier');

        $zalo_token = $helper->getZaloToken($code_verifier); // get zalo token

        $access_token = $zalo_token->getAccessToken();


        $params = ['fields' => 'id,name,picture'];
        $response = $zalo->get(ZaloEndpoint::API_GRAPH_ME, $access_token, $params);
        $result = $response->getDecodedBody(); // result

        $service = new SocialAccountService;
        $user = $service->createOrGetUserFromZalo($result);

        Auth::login($user);

        return redirect()->intended('/');
    }

    public function redirectToOaZalo()
    {


        $config = array(
            'app_id' => config("services.zalo.client_id"),
            'app_secret' =>  config("services.zalo.client_secret")
        );
        $zalo = new Zalo($config);
        $helper = $zalo->getRedirectLoginHelper();
        $callback_url =   config("services.zalo.redirect_oa"); //"https://shipdemo.thienlong.vn/auth/zalo/callback";
        $code_verifier = Generator::codeVerifier();
        session()->put('codeVerifier', $code_verifier);
        $code_challenge = Generator::codeChallenge($code_verifier);
        $state = uniqid();
        $loginUrl = $helper->getLoginUrlByOA($callback_url, $code_challenge, $state);

        return redirect($loginUrl);
    }
    public function handleOaZaloCallback(Request $request)//Redirect Call back get access_token for OA 
    {
        $msg_builder = new MessageBuilder(MessageBuilder::MSG_TYPE_TXT);
        $config = array(
            'app_id' => config("services.zalo.client_id"),
            'app_secret' =>  config("services.zalo.client_secret")
        );

        $zalo = new Zalo($config);


        $helper = $zalo->getRedirectLoginHelper();
        $callback_url =   config("services.zalo.redirect_oa"); // $ZALO_REDIRECT_UR OAI ;
        $code_verifier = session()->get('codeVerifier');

        $zalo_token = $helper->getZaloTokenByOA($code_verifier); // get zalo token

        $access_token = $zalo_token->getAccessToken();
        // dd($access_token);//Yêu cầu mã truy cập mức OA
        //Lưu access token vào DB
        $params = ['fields' => 'id,name,picture'];
        $response = $zalo->get(ZaloEndpoint::API_GRAPH_ME, $access_token, $params);
        $result = $response->getDecodedBody(); // result

        $msg_builder = new MessageBuilder(MessageBuilder::MSG_TYPE_TXT);
        $msg_builder->withUserId($result['id']);
        $msg_builder->withText('Đã cấp quyền truy cập ứng dụng ship');
        $msg_text = $msg_builder->build();

        // send request
        $response = $zalo->post(ZaloEndPoint::API_OA_SEND_CONSULTATION_MESSAGE_V3,$access_token, $msg_text);
        $result = $response->getDecodedBody();
        dd($result);
        // $service = new SocialAccountService;
        // $user = $service->createOrGetUserFromZalo($result);

        // Auth::login($user);

        return redirect()->intended('/');
    }
}
