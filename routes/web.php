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

use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Auth::routes();
});

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('/home', HomeController::class.'@index')->name('home');
    Route::get('/bot-config', HomeController::class.'@edit')->name('config');
    Route::post('/bot-config', HomeController::class.'@store');
});
