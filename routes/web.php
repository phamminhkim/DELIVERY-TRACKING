<?php

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
Auth::routes(['verify' => true]);
Route::get('/', function () {
  return view('welcome');
});
Route::get('/calllback', function ($state) {
    dd("calllback");
});
Route::get('/zalo', function () {
      $config = array(
        'app_id' => '270349389225145785',
        'app_secret' => 'PtCJyx1eNY4Pr6W467Xu'
    );
    $zalo = new Zalo($config);
    $helper = $zalo -> getRedirectLoginHelper();
    $callbackUrl = "https://shipdemo.thienlong.vn/login/zalo/callback";

    $codeVerifier =   Str::random(43) ;//bin2hex(random_bytes(32));
       $codeChallenge = hash('sha256', $codeVerifier, true);
       $state =  Str::random(40);


      
$codeVerifier = Generator::codeVerifier();

$codeChallenge = Generator::codeChallenge($codeVerifier);

       $verifierBytes = random_bytes(64);
       $codeVerifier = rtrim(strtr(base64_encode($verifierBytes), "+/", "-_"), "=");
      // dd($codeChallenge);
       // Very important, "raw_output" must be set to true or the challenge
       // will not match the verifier.
     //  $challengeBytes = hash("sha256", $codeVerifier, true);
      // $codeChallenge = rtrim(strtr(base64_encode($challengeBytes), "+/", "-_"), "=");
   
       // State token, a uuid is fine here
       $state = uniqid();



    //dd($codeChallenge . " - ".$state);
   // Log::info("test call back");
    $loginUrl = $helper->getLoginUrl($callbackUrl, $codeChallenge, $state);

   return redirect($loginUrl);
    

    //$codeVerifier = PKCEUtil::genCodeVerifier();
    //$codeChallenge = PKCEUtil::genCodeChallenge($codeVerifier);
    //$codeVerifier = "your code verifier";
    $zaloToken = $helper->getZaloToken($codeVerifier); // get zalo token
   
    $accessToken = $zaloToken->getAccessToken();
    dd($accessToken);
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Page routes 

// Route::get('/myinfo', function () {
//   return view('profile.myinfo');
// });

Route::get('/myinfo', function () {
  $jsonString = file_get_contents(base_path('resources/data/myInfo.json'));
  $fakeUser = json_decode($jsonString, true);

  return view('pages.profile.components.formInfo')->with('fakeUser', $fakeUser);
});

Route::get('/myorder', function () {
  $jsonString = file_get_contents(base_path('resources/data/myOrder.json'));
  $fakeData = json_decode($jsonString, true);

  return view('pages.orders.myorder')->with('fakeData', $fakeData);
});


Route::get('/myorder-detail', function () {
  $jsonString = file_get_contents(base_path('resources/data/timeline.json'));
  $fakeTimeline = json_decode($jsonString, true);

  return view('pages.orders.orderDetail')->with('fakeTimeline', $fakeTimeline);
});

Route::get('/order-waiting', function () {
  return view('pages.orders.orderWaiting');
});

Route::get('login/{social}', [
  'as' => 'login.{social}',
  'uses' => 'SocialAuthController@redirectToProvider'
]);

Route::get('login/{social}/callback', [
  'as' => 'login.{social}.callback',
  'uses' => 'SocialAuthController@handleProviderCallback'
]);

Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');