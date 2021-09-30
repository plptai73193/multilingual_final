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

Route::namespace("Api")->group(function() {
    //Route::get("/", "MallController@store")->name('store');
    //Route::get("/install", "MallController@store")->name('store');
    Route::get("/", function(){return view('install'); });
    Route::get("/install", function(){return view('install'); });
});

// Route::get("/install", function() {
//     return view('install');
// });

Route::get("/app/{mall_params}", function($mall_params) {
    return view('app', ["mall_params" => $mall_params]);
})->name('app');