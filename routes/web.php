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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Page routes 

// Route::get('/myinfo', function () {
//   return view('profile.myinfo');
// });

Route::get('/myinfo', function () {
  $jsonString = file_get_contents(base_path('resources/data/myInfo.json'));
  $fakeUser = json_decode($jsonString, true);

  return view('profile.components.formInfo')->with('fakeUser', $fakeUser);
});

Route::get('/myorder', function () {
  $jsonString = file_get_contents(base_path('resources/data/myOrder.json'));
  $fakeData = json_decode($jsonString, true);

  return view('orders.myorder')->with('fakeData', $fakeData);
});

Route::get('/myorder-detail', function () {
  $jsonString = file_get_contents(base_path('resources/data/timeline.json'));
  $fakeTimeline = json_decode($jsonString, true);

  return view('orders.orderDetail')->with('fakeTimeline', $fakeTimeline);
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