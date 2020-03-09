<?php

use Illuminate\Http\Request;

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

Route::apiResource('user','UserController');
Route::post('verifyUser','UserController@verifyUser');
Route::get('getApiStores','UserController@getApiStores');

Route::apiResource('product','ProductController');
Route::post('storeViaModal', 'ProductController@storeViaModal');
Route::post('getProductsFromApiOrScrapper', 'ProductController@getProductsFromApiOrScrapper');
Route::post('getAdminProducts', 'ProductController@getAdminProducts'); 
Route::get('getCategories', 'ProductController@getCategories');
Route::get('getProductsByCategoryId', 'ProductController@getProductsByCategoryId');
Route::get('getProductsByVendorId', 'ProductController@getProductsByVendorId');
Route::get('getVendorProductsByCategoryId', 'ProductController@getVendorProductsByCategoryId');
Route::get('setHistory', 'ProductController@setHistory');
Route::get('getHistory', 'ProductController@getHistory');
Route::get('setFavourites', 'ProductController@setFavourites');
Route::get('getFavourites', 'ProductController@getFavourites');







