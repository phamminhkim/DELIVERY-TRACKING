<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController\ResponseController;
use App\Http\Controllers\Controller;
use App\User;
use App\Providers\RouteServiceProvider;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends ResponseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
         $this->middleware('guest')->except('logout');
    }
    protected $dbUser = null;
    protected $login_field = 'username';

    protected function credentials(Request $request)
    {

        $this->dbUser = User::where('email', $request->username)->first();
        if ($this->dbUser) {

            $this->login_field = 'email';
            $credentails['email'] = $request->username;
            $credentails['password'] = $request->password;
        } else {

            $this->dbUser = User::where('username', $request->username)->first();
            $credentails['username'] = $request->username;
            $credentails['password'] = $request->password;
            // $credentails =  $request->only($this->login_field, 'password');
            $this->login_field = 'username';
        }
        if ($this->dbUser) {
            return $credentails; // $request->only($this->username(), 'password');
        } else {
            return [];
        }
    }
    public function login(Request $request)
    {

        $this->validateLogin($request);
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );
            return $this->sendFailedWithMessage('Logging in too many times. Please wait in ' .  $seconds . ' seconds');
        }
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }
        $this->incrementLoginAttempts($request);
        return $this->sendFailedWithMessage('Login failed');
    }
    public function logout(Request $request)
    {
         //Ghi đè và Không xử lý
    }
    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
      //  $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        $user = Auth::user();
        /// dd($user);
        $access_token = $user->createToken('authToken')->accessToken;
        $res['access_token'] = $access_token;
        return $this->sendResponse($res, 'Logged in successfully');
    }
    protected function validateLogin(Request $request)
    {

        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function username()
    {
        return "username";
    }
}
