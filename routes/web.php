<?php


use App\Http\Controllers\Api\Business\ApplicationController;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Zalo\Zalo;
use Illuminate\Support\Str;
use Wilkques\PKCE\Generator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//
Auth::routes(['verify' => true]);


Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::any('/{any?}', 'SinglePage\AppController@index')->where('any', '.*');

Route::prefix('admins')->group(function () {
    Route::get('routes', function () {
        $routeCollection = Route::getRoutes();

        echo "<table style='width:100%'>";
        echo "<tr>";
        echo "<td width='10%'><h4>HTTP Method</h4></td>";
        echo "<td width='10%'><h4>Route</h4></td>";
        echo "<td width='10%'><h4>Name</h4></td>";
        echo "<td width='70%'><h4>Corresponding Action</h4></td>";
        echo "</tr>";
        foreach ($routeCollection as $value) {
            echo "<tr>";
            echo "<td>" . $value->methods()[0] . "</td>";
            echo "<td>" . $value->uri() . "</td>";
            echo "<td>" . $value->getName() . "</td>";
            echo "<td>" . $value->getActionName() . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    });

    Route::get('info', function () {
        $jsonString = file_get_contents(base_path('resources/data/myInfo.json'));
        $fakeUser = json_decode($jsonString, true);

        return view('pages.profile.components.formInfo')->with('fakeUser', $fakeUser);
    });

    Route::get('orders', function () {
        $jsonString = file_get_contents(base_path('resources/data/myOrder.json'));
        $fakeData = json_decode($jsonString, true);

        return view('pages.orders.myorder')->with('fakeData', $fakeData);
    });


    Route::get('order-detail', function () {
        $jsonString = file_get_contents(base_path('resources/data/timeline.json'));
        $fakeTimeline = json_decode($jsonString, true);

        return view('pages.orders.orderDetail')->with('fakeTimeline', $fakeTimeline);
    });

    Route::get('order-waiting', function () {
        return view('pages.orders.orderWaiting');
    });
    Route::get('/auth/oazalo', 'Auth\SocialAuthController@redirectToOaZaloAuthorizeUrl');
    Route::get('/auth/oazalo/callback', 'Auth\SocialAuthController@handleOaZaloCallback');
});

Route::get('login/{social}', [
    'as' => 'login.{social}',
    'uses' => 'Auth\SocialAuthController@redirectToProvider'
]);

Route::get('login/{social}/callback', [
    'as' => 'login.{social}.callback',
    'uses' => 'Auth\SocialAuthController@handleProviderCallback'
]);
Route::get('/auth/zalo', 'Auth\SocialAuthController@redirectToUserZaloAuthorizeUrl')->name('zalo.login');
Route::get('/auth/zalo/callback', 'Auth\SocialAuthController@handleUserZaloCallback');



Route::get('/scan-qr/{qr_code}', [ApplicationController::class, 'getTargetApplicationUrl']);

Route::get('access-token', function () {
    $auth_user = Auth()->user();

    $access_token = null;
    // Nếu không phải login từ API thì tạo token cho user
    if ($auth_user && !$auth_user->token()) {
        if (Session::has('user')) {
            $access_token = Session::get('user');
        } else {
            $authToken =  $auth_user->createToken('authToken');
            $access_token = $authToken->accessToken; // $auth_user->createToken('authToken')->accessToken;
            $auth_user->withAccessToken($access_token);
            Session::put('user', $access_token);
        }
    }

    echo $access_token ?? "Không có token";
});
