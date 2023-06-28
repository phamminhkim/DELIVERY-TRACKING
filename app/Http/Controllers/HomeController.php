<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController\WebBaseController;
use Illuminate\Http\Request;

class HomeController extends WebBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            //load menu từ Role_user
            $auth_user = Auth()->user();

            $access_token = "";
            // Nếu không phải login từ API thì tạo token cho user
            if (!$auth_user->token()) {
                if ($request->session() && $request->session()->get('user')) {
                    $access_token = $request->session()->get('user');
                } else {
                    $authToken =  $auth_user->createToken('authToken');
                    $access_token = $authToken->accessToken; // $auth_user->createToken('authToken')->accessToken;
                    $auth_user->withAccessToken($access_token);
                    $request->session()->put('user', $access_token);
                }
            }
           
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    // public function Order()
    // {
    //     return view('order');
    // }

}
