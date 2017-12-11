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
Route::match(['get', 'post'], '/payroll', 'PayrollController@index')->name('payroll');
Route::match(['get', 'post'], '/profile', 'ProfileController@index');
Route::match(['get', 'post'], '/message', 'MessageController@index');
Route::match(['get', 'post'], '/admin/{table}', 'AdminController@index');
Route::get('/pending', function () {
    if (Auth::user()->isVerified()) return back();
    return view('auth.pending');
})->middleware('auth');

//todo use controller to deal with file including pdf file
Route::get('/receipts/{name}', function ($name) {
    //header("Content-type: " . str_contains($name, 'pdf') ? "application/pdf" : "image/*");
    if (str_contains($name, 'pdf')) {
        header("Content-type:application/pdf");
    } else {
        header("Content-type:image/*");
    }

    echo Storage::get('receipts/' . $name);
});

Route::get('/test', 'TestController@index')->name('test');
