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

// 
Auth::routes(['verify' => true]);
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('admins')->group(function () {
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
});

Route::get('login/{social}', [
    'as' => 'login.{social}',
    'uses' => 'SocialAuthController@redirectToProvider'
  ]);
  
  Route::get('login/{social}/callback', [
    'as' => 'login.{social}.callback',
    'uses' => 'SocialAuthController@handleProviderCallback'
  ]);
  Route::get('/auth/zalo', 'SocialAuthController@redirectToZalo')->name('zalo.login');
  Route::get('/auth/zalo/callback', 'SocialAuthController@handleZaloCallback');
   
