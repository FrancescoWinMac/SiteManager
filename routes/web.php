<?php

use App\Http\Controllers\SitoController;
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


Auth::routes();

Route::group(['middleware'=>'auth'],function(){
    Route::get('/', 'HomeController@index');
    Route::get('/account', 'UserController@setting');
    Route::get('/settings', 'SettingController@index');
    Route::get('/search', 'SearchController@index');
    Route::get('/siti', 'SitoController@index');

});

