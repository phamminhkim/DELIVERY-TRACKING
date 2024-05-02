<?php

namespace App\Http\Controllers\BaseController;

use App\Models\System\Route;
use Illuminate\Http\Request;
use App\Utilities\MenuUtility;
use App\Models\Shared\ConfigUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WebBaseController  extends ResponseController
{
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
               //Cấu hình expand_menu left
               $config_user  =  ConfigUser::where('user_id', $auth_user->id)->where('code', MenuUtility::$EXPAND_LEFT_MENU)->first();

               $expand_menu = "sidebar-mini layout-fixed";
               $expand_menu_value = 0;
               if ($config_user) {
                   if ($config_user->value == 1) {
                       $expand_menu_value = 1;
                       $expand_menu = 'sidebar-mini layout-fixed sidebar-collapse';
                   } else {
                       $expand_menu_value = 0;
                       $expand_menu = 'sidebar-mini layout-fixed';
                   }
               }
               
            $menus_and_routes = MenuUtility::getMenusAndRoutesForUser(Auth::user()->id);
            $menus = $menus_and_routes['menus'];
            $routes = $menus_and_routes['routes'];
            $version = env('FRONTEND_VERSION', 1);
            
            view()->share(compact('access_token', 'menus', 'routes', 'version','expand_menu_value','expand_menu'));
            return $next($request);
        });
    }
    public function sendResponse($result, $message = '', $code = 200)
    {

        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
    public function sendError($error, $errorMessage = [], $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $error
        ];
        if (!empty($errorMessage)) {
            $response['errors'] = $errorMessage;
        }
        return response()->json($response, $code);
    }

    public function sendOk()
    {
        return response();
    }
    public function sendSuccess($message)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];
        return response()->json($response, 200);
    }
    public function sendFailedWithMessage($message)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        return response()->json($response, 200);
    }
    public function sendFailedWithStatusCode($message, $code)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        return response()->json($response, $code);
    }
}
