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
        $code_verifier = Generator::code_verifier();
        session()->put('code_verifier', $code_verifier);
        $codeChallenge = Generator::codeChallenge($code_verifier);
        $state = uniqid();
        $login_url = $helper->getLoginUrl($callback_url, $codeChallenge, $state);

        return redirect($login_url);
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
        $code_verifier = session()->get('code_verifier');

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
        $code_verifier = Generator::code_verifier();
        session()->put('code_verifier', $code_verifier);
        $codeChallenge = Generator::codeChallenge($code_verifier);
        $state = uniqid();
       // $login_url = $helper->getLoginUrl($callback_url, $codeChallenge, $state);
        $login_url = $helper->getLoginUrlByOA($callback_url, $codeChallenge, $state);

        return redirect($login_url);
    }
    public function handleOaZaloCallback(Request $request)//Redirect Call back get access_token for OA 
    {
       
        $config = array(
            'app_id' => config("services.zalo.client_id"),
            'app_secret' =>  config("services.zalo.client_secret")
        );

        $zalo = new Zalo($config);
        $helper = $zalo->getRedirectLoginHelper();
        $callback_url =   config("services.zalo.redirect_oa"); // $ZALO_REDIRECT_UR OAI ;
        $code_verifier = session()->get('code_verifier');
        $zalo_token = $helper->getZaloTokenByOA($code_verifier); // get zalo token
        $access_token = $zalo_token->getAccessToken();
        $params = ['fields' => 'id,name,picture'];
        $response = $zalo->get(ZaloEndpoint::API_GRAPH_ME, $access_token, $params);
        $result = $response->getDecodedBody(); // result
        
        $msgBuilder = new MessageBuilder(MessageBuilder::MSG_TYPE_TXT);
        $msgBuilder->withUserId($result['id']);
        $msgBuilder->withText('Đã cấp quyền truy cập ứng dụng ship');
        $msgText = $msgBuilder->build();

        // send request
        $response = $zalo->post(ZaloEndPoint::API_OA_SEND_CONSULTATION_MESSAGE_V3,$access_token, $msgText);
        $result = $response->getDecodedBody();
        dd($result);
        // $service = new SocialAccountService;
        // $user = $service->createOrGetUserFromZalo($result);

        // Auth::login($user);

        // return redirect()->intended('/');
    }
}
