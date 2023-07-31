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

Route::group([], function(){
    Route::fallback(function () {
        session()->flush();
        return view('404');
    })->name('site.404');
});

Route::get('/', 'App\Http\Controllers\Login@index')->name('site.login');
Route::post('/doLogin', 'App\Http\Controllers\Login@doLogin')->name('site.doLogin');
Route::get('/recoverPwd', 'App\Http\Controllers\Login@recoverPassword')->name('site.recoverPwd');
Route::post('/doRecoverPwd', 'App\Http\Controllers\Login@doRecoverPassword')->name('site.doRecoverPwd');
Route::get('/changeNewPwd/{idKey}', 'App\Http\Controllers\Login@changeNewPwd')->name('site.changeNewPwd');
Route::post('/doChangeNewPwd', 'App\Http\Controllers\Login@doChangeNewPwd')->name('site.doChangeNewPwd');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\Dashboard@index')->name('site.dashboard');

    Route::prefix('client')->group(function () {
        Route::get('/', 'App\Http\Controllers\Client@index')->name('client.index');
    });
});