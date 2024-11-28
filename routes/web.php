<?php


use App\Http\Controllers\Api\Business\ApplicationController;
use App\Http\Controllers\Api\Master\CustomerPartnerController;
use App\Http\Controllers\Api\Master\SapMaterialMappingController;
use App\Http\Controllers\Api\Master\SapMaterialController;
use App\Http\Controllers\Api\Master\MaterialDonatedController;
use App\Http\Controllers\Api\Master\MaterialComboController;
use App\Http\Controllers\Auth\JstController;
use App\Http\Controllers\SinglePage\AppController;
use App\Models\Master\CustomerPartner;
use App\Models\Master\MaterialCombo;
use App\Models\Master\MaterialDonated;
use Illuminate\Contracts\Session\Session;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
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
// Auth::routes(['verify' => true]);


// Auth::routes();
// Route::get('/', function () {
//     return Redirect::to('/dashboard');
// });

// Route::prefix('admins')->group(function () {
//     Route::get('routes', function () {
//         $routeCollection = Route::getRoutes();

//         echo "<table style='width:100%'>";
//         echo "<tr>";
//         echo "<td width='10%'><h4>HTTP Method</h4></td>";
//         echo "<td width='10%'><h4>Route</h4></td>";
//         echo "<td width='10%'><h4>Name</h4></td>";
//         echo "<td width='70%'><h4>Corresponding Action</h4></td>";
//         echo "</tr>";
//         foreach ($routeCollection as $value) {
//             echo "<tr>";
//             echo "<td>" . $value->methods()[0] . "</td>";
//             echo "<td>" . $value->uri() . "</td>";
//             echo "<td>" . $value->getName() . "</td>";
//             echo "<td>" . $value->getActionName() . "</td>";
//             echo "</tr>";
//         }
//         echo "</table>";
//     });
//     Route::get('/auth/oazalo', 'Auth\SocialAuthController@redirectToOaZaloAuthorizeUrl');
//     Route::get('/auth/oazalo/callback', 'Auth\SocialAuthController@handleOaZaloCallback');
// });

// Route::get('login/{social}', [
//     'as' => 'login.{social}',
//     'uses' => 'Auth\SocialAuthController@redirectToProvider'
// ]);

// Route::get('login/{social}/callback', [
//     'as' => 'login.{social}.callback',
//     'uses' => 'Auth\SocialAuthController@handleProviderCallback'
// ]);
// Route::get('/auth/zalo', 'Auth\SocialAuthController@redirectToUserZaloAuthorizeUrl')->name('zalo.login');
// Route::get('/auth/zalo/callback', 'Auth\SocialAuthController@handleUserZaloCallback');

// Route::get('/auth/onetl', 'Auth\SocialAuthController@redirectToOnetlUrl');
// Route::get('/auth/onetl/callback', 'Auth\SocialAuthController@handleOnetlCallback');


// Route::get('/scan-qr/{qr_code}', [ApplicationController::class, 'getTargetApplicationUrl']);
// Route::get('/excel/{filename}', [SapMaterialMappingController::class,'download']);
// Route::get('/excel/{filename}', [SapMaterialController::class,'download']);
// Route::get('/excel/{filename}', [CustomerPartnerController::class,'download']);
// Route::get('/excel/{filename}', [MaterialDonatedController::class,'download']);
// Route::get('/excel/{filename}', [MaterialComboController::class,'download']);
// Route::get('/profile', [AppController::class,'profile']);

// Route::get('access-token', function () {
//     $auth_user = Auth()->user();

//     $access_token = null;
//     // Nếu không phải login từ API thì tạo token cho user
//     if ($auth_user && !$auth_user->token()) {
//         if (Session::has('user')) {
//             $access_token = Session::get('user');
//         } else {
//             $authToken =  $auth_user->createToken('authToken');
//             $access_token = $authToken->accessToken; // $auth_user->createToken('authToken')->accessToken;
//             $auth_user->withAccessToken($access_token);
//             Session::put('user', $access_token);
//         }
//     }

//     echo $access_token ?? "Không có token";
// });
// Route::get('/jst/generate-auth-url', [JstController::class, 'generateAuthorizationUrl']);
Route::get('/jst/callback', [JstController::class, 'handleCallback']);
// Route::get('/jst/refresh-token', [JstController::class, 'refreshToken']);

Route::any('/{any?}', 'SinglePage\AppController@index')->where('any', '.*');
