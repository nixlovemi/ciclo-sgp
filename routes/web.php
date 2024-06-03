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
        Route::get('/resetPwd/{codedId}', 'App\Http\Controllers\User@resetPwd')->name('user.resetPwd');
        Route::post('/doResetPwd', 'App\Http\Controllers\User@doResetPwd')->name('user.doResetPwd');
        Route::get('/profile', 'App\Http\Controllers\User@profile')->name('user.profile');
        Route::post('/saveProfile', 'App\Http\Controllers\User@saveProfile')->name('user.saveProfile');
    });

    Route::prefix('job')->group(function () {
        Route::get('/', 'App\Http\Controllers\Job@index')->name('job.index');
        Route::get('/view/{codedId}', 'App\Http\Controllers\Job@view')->name('job.view');
        Route::get('/add', 'App\Http\Controllers\Job@add')->name('job.add');
        Route::post('/add', 'App\Http\Controllers\Job@doAdd')->name('job.doAdd');
        Route::get('/edit/{codedId}', 'App\Http\Controllers\Job@edit')->name('job.edit');
        Route::post('/edit/{codedId}', 'App\Http\Controllers\Job@doEdit')->name('job.doEdit');
        Route::get('/briefingPdf/{codedId}', 'App\Http\Controllers\Job@briefingPdf')->name('job.briefingPdf');
    });

    Route::prefix('jobFile')->group(function () {
        Route::get('/add/{jobCodedId}/{json}/{codedJobSection?}', 'App\Http\Controllers\JobFile@add')->name('jobFile.add');
        Route::post('/add', 'App\Http\Controllers\JobFile@doAdd')->name('jobFile.doAdd');
    });

    Route::prefix('serviceItems')->group(function () {
        Route::get('/', 'App\Http\Controllers\ServiceItem@index')->name('serviceItems.index');
        Route::get('/view/{codedId}', 'App\Http\Controllers\ServiceItem@view')->name('serviceItems.view');
        Route::get('/add', 'App\Http\Controllers\ServiceItem@add')->name('serviceItems.add');
        Route::post('/add', 'App\Http\Controllers\ServiceItem@addSave')->name('serviceItems.add.save');
        Route::get('/edit/{codedId}', 'App\Http\Controllers\ServiceItem@edit')->name('serviceItems.edit');
        Route::post('/edit/{codedId}', 'App\Http\Controllers\ServiceItem@editSave')->name('serviceItems.edit.save');
    });

    Route::prefix('quote')->group(function () {
        Route::get('/', 'App\Http\Controllers\Quote@index')->name('quote.index');
        Route::get('/add/{codedId?}', 'App\Http\Controllers\Quote@add')->name('quote.add');
        Route::post('/add', 'App\Http\Controllers\Quote@doAdd')->name('quote.doAdd');
        Route::get('/linkToJobHtml', 'App\Http\Controllers\Quote@getLinkToJobHtml')->name('quote.getLinkToJobHtml');
        Route::post('/doLinkToJobHtml', 'App\Http\Controllers\Quote@saveLinkToJobHtml')->name('quote.saveLinkToJobHtml');
        Route::get('/quoteItemsHtml', 'App\Http\Controllers\Quote@getQuoteItemsHtml')->name('quote.quoteItemsHtml');
        Route::post('/addFromJob', 'App\Http\Controllers\Quote@addFromJob')->name('quote.addFromJob');
        Route::post('/removeFromJob', 'App\Http\Controllers\Quote@removeFromJob')->name('quote.removeFromJob');
        Route::get('/pdf/{codedId}', 'App\Http\Controllers\Quote@pdf')->name('quote.pdf');
    });

    Route::prefix('quoteItem')->group(function () {
        Route::get('/add', 'App\Http\Controllers\QuoteItem@add')->name('quoteItem.add');
        Route::post('/add', 'App\Http\Controllers\QuoteItem@doAdd')->name('quoteItem.doAdd');
    });
});

// show jobs
Route::get('/showJobs', 'App\Http\Controllers\Dashboard@showJobs')->name('site.showJobs');