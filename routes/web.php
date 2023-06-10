<?php

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