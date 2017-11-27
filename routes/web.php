<?php

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

use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('hour', 'HoursController');
Route::resource('expense', 'ExpenseController');
Route::resource('engagement', 'EngagementController');
Route::get('/test', 'TestController@index')->name('test');
Route::get('/receipts/{name}', function ($name) {
    header("Content-type: image/jpeg");
    echo Storage::get('receipts/' . $name);
});
Route::match(['get', 'post'], '/profile', 'ProfileController@index');
Route::match(['get', 'post'], '/admin/{table}', 'AdminController@index');
Route::match(['get', 'post'], '/notification', 'NotificationController@index');
Route::get('/pending',function (){
   return view('auth.pending');
});
