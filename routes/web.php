<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Route::get('/myinfo', function () {
    $locale = App::getLocale();
    // dd( $locale );
    return view('profile.myinfo');
})->middleware('verified');
Route::get('/myorder', function () {
    return view('orders.myorder');
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
