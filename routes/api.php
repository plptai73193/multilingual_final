<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Middleware\Cafe24Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware(Cafe24Auth::class)->prefix('v1')->namespace("Api")->group(function() {
    /* mall endpoints */
    Route::prefix('mall')->group(function() {
        //Endpoint: api/v1/mall/store
        Route::post('store', 'MallController@store')->name('mall.store');
        //Endpoint: api/v1/mall/text
        Route::post('text', 'TextController@index')->name('mall.text');
        //Endpoint: api/v1/mall/delete
        Route::post('delete', 'TextController@delete')->name('mall.delete');
        //Endpoint: api/v1/mall/translatetext
        Route::get('translatetext', 'TextController@translatetext')->name('mall.translatetext');
        //Endpoint: api/v1/mall/translatetable
        Route::get('translatetable', 'TextController@getAppTable')->name('mall.table');
        //Endpoint: api/v1/mall/search
        Route::get('search', 'TextController@search')->name('mall.search');
        //Endpoint: api/v1/mall/productdetail
        Route::get('productdetail', 'TextController@productdetail')->name('mall.productdetail');
    });
});