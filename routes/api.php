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
Route::post('getProductsFromSharafDGAPI', 'ProductController@getProductsFromSharafDGAPI');
Route::post('getProductTypes', 'ProductController@getProductTypes');
Route::post('getProductCategories', 'ProductController@getProductCategories');
Route::post('getProductSubcategories', 'ProductController@getProductSubcategories');
Route::post('getAdminProducts', 'ProductController@getAdminProducts');   
Route::get('getSubCategory', 'ProductController@getSubCategory');
Route::get('getParentCategory', 'ProductController@getParentCategory');


