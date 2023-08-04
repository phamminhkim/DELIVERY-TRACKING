<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Providers\RouteServiceProvider;
 
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
class LoginController extends Controller
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

    use AuthenticatesUsers ;

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
    protected $login_field = 'email';
    
    protected function credentials(Request $request)
    {
      
        $this->dbUser = User::where('email', $request->email)->first();
       
        if ($this->dbUser) {
            $credentails['email'] = $request->email;
            $credentails['password'] = $request->password;
        }else{
            $this->dbUser = User::where('username', $request->email)->first();
            $credentails['username'] = $request->email;
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
    protected function validateLogin(Request $request)
    {
        
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }
      /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
     protected function authenticated(Request $request, $user)
     {   
      
         //if (!$user->email_verified_at != null) {
         //
         //    auth()->logout();
         //    return back()->with('warning', 'Chúng tôi đã gửi email đến tài khoản của bạn, vui lòng xác thực để kích hoạt tài khoản.');
         //}
         return redirect()->intended($this->redirectPath());
     }
    public function username()
    {
        return $this->login_field;
    }
}
