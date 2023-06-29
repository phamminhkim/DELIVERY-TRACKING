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
        $callbackUrl =   config("services.zalo.redirect"); //"https://shipdemo.thienlong.vn/auth/zalo/callback";
        $codeVerifier = Generator::codeVerifier();
        session()->put('codeVerifier', $codeVerifier);
        $codeChallenge = Generator::codeChallenge($codeVerifier);
        $state = uniqid();
        $loginUrl = $helper->getLoginUrl($callbackUrl, $codeChallenge, $state);

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
        $callbackUrl =   config("services.zalo.redirect"); // $ZALO_REDIRECT_URI ;
        $codeVerifier = session()->get('codeVerifier');

        $zaloToken = $helper->getZaloToken($codeVerifier); // get zalo token

        $accessToken = $zaloToken->getAccessToken();


        $params = ['fields' => 'id,name,picture'];
        $response = $zalo->get(ZaloEndpoint::API_GRAPH_ME, $accessToken, $params);
        $result = $response->getDecodedBody(); // result

        $service = new SocialAccountService;
        $user = $service->createOrGetUserFromZalo($result);

        Auth::login($user);

        return redirect()->intended('/');
    }
    public function handleOaZaloCallback(Request $request)//Redirect Call back get access_token for OA 
    {
        
        $config = array(
            'app_id' => config("services.zalo.client_id"),
            'app_secret' =>  config("services.zalo.client_secret")
        );

        $zalo = new Zalo($config);


        $helper = $zalo->getRedirectLoginHelper();
        $callbackUrl =   config("services.zalo.redirect_oa"); // $ZALO_REDIRECT_UR OAI ;
        $codeVerifier = session()->get('codeVerifier');

        $zaloToken = $helper->getZaloToken($codeVerifier); // get zalo token

        $accessToken = $zaloToken->getAccessToken();

        
        $params = ['fields' => 'id,name,picture'];
        $response = $zalo->get(ZaloEndpoint::API_GRAPH_ME, $accessToken, $params);
        $result = $response->getDecodedBody(); // result

        $service = new SocialAccountService;
        $user = $service->createOrGetUserFromZalo($result);

        Auth::login($user);

        return redirect()->intended('/');
    }
}
