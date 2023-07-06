<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Repositories\SystemRepository;
use App\Services\SocialAccountService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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

    public function redirectToUserZaloAuthorizeUrl(Request $request)
    {
        $handler = SystemRepository::zaloRequest($request);
        $authorize_url = $handler->UserAuthorizeUrl();

        if ($authorize_url) {
            return redirect($authorize_url);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function handleUserZaloCallback(Request $request)
    {
        $handler = SystemRepository::zaloRequest($request);
        $is_success = $handler->UserCallback();

        if ($is_success) {
            return redirect()->intended('/');
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function redirectToOaZaloAuthorizeUrl(Request $request)
    {
        $handler = SystemRepository::zaloRequest($request);
        $authorize_url = $handler->OaAuthorizeUrl();

        if ($authorize_url) {
            return redirect($authorize_url);
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }

    public function handleOaZaloCallback(Request $request) //Redirect Call back get access_token for OA 
    {
        $handler = SystemRepository::zaloRequest($request);
        $is_success = $handler->OaCallback();

        if ($is_success) {
            return redirect()->intended('/');
        } else {
            return $this->responseError($handler->getMessage(), $handler->getErrors());
        }
    }
}
