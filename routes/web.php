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

// ==============================================
// ALL ROUTES MUST HAVE NAME FOR PERMISSION CHECK
// ==============================================
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

// ================================================
// ADD ROUTE PERMISSIONS ON App\Helpers\Permissions
// ================================================
Route::middleware(['authWeb'])->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\Dashboard@index')->name('site.dashboard');

    Route::prefix('client')->group(function () {
        Route::get('/', 'App\Http\Controllers\Client@index')->name('client.index');
        Route::get('/view/{codedId}', 'App\Http\Controllers\Client@view')->name('client.view');
        Route::get('/add', 'App\Http\Controllers\Client@add')->name('client.add');
        Route::post('/add', 'App\Http\Controllers\Client@addSave')->name('client.add.save');
        Route::get('/edit/{codedId}', 'App\Http\Controllers\Client@edit')->name('client.edit');
        Route::post('/edit/{codedId}', 'App\Http\Controllers\Client@editSave')->name('client.edit.save');
    });

    Route::prefix('user')->group(function () {
        Route::get('/', 'App\Http\Controllers\User@index')->name('user.index');
        Route::get('/view/{codedId}', 'App\Http\Controllers\User@view')->name('user.view');
        Route::get('/add', 'App\Http\Controllers\User@add')->name('user.add');
        Route::post('/add', 'App\Http\Controllers\User@addSave')->name('user.add.save');
        Route::get('/edit/{codedId}', 'App\Http\Controllers\User@edit')->name('user.edit');
        Route::post('/edit/{codedId}', 'App\Http\Controllers\User@editSave')->name('user.edit.save');
        Route::get('/changePwd', 'App\Http\Controllers\User@changePwd')->name('user.changePwd');
        Route::post('/doChangePwd', 'App\Http\Controllers\User@doChangePwd')->name('user.doChangePwd');
    });
});