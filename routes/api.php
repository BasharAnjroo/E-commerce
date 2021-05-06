<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\API\CategoryController as APICategoryController;
use App\Http\Controllers\API\CityController;
use App\Http\Controllers\API\CurrencyController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\ResetePWDController;
use App\Http\Controllers\Auth\UpdatePWDController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
 */

//	############# Begin  Authentication User ###################################

Route::group(['middleware' => ['api', 'cors'], 'namespace' => 'Auth'], function () {
    Route::post('login', 'AuthController@loginUser');
    Route::post('register', 'AuthController@UserRegister');
    Route::post('req-password-reset', [ResetePWDController::class, 'forgot']);
    Route::post('update-password', [UpdatePWDController::class, 'updatePassword']);

    Route::get('/user/verify/{token}', 'AuthController@verifyUser');
});
Route::group(['middleware' => ['auth:api', 'cors'], 'namespace' => 'Auth', 'prefix' => 'user'], function () {
    Route::get('logout', 'AuthController@logout');
});

//	############# END  Authentication User ###################################

//	############# Begin  Authentication Admin ###################################
Route::group(['middleware' => ['cors'], 'namespace' => 'Auth', 'prefix' => 'admin'], function () {
    Route::post('login', 'AuthController@login');
});
Route::group(['middleware' => ['auth:api', 'cors'], 'namespace' => 'Auth', 'prefix' => 'admin'], function () {
    Route::get('logout', 'AuthController@logout');
});

//	############# END  Authentication Admin ###################################

//	############# Begin  API  Admin ###################################
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::group(['middleware' => ['auth:api', 'role', 'cors', 'json.response']], function () {
        Route::middleware(['scope:Admin'])->group(function () {
            //////// Category//////////
            Route::resource('category', 'CategoryController');
            //////// Product //////////
            Route::resource('product', 'ProductController');
            Route::post('image_store', 'ProductController@image_store');
            Route::delete('image_delete/{id}', 'ProductController@image_delete');
            //////// Attribute //////////
            Route::resource('Attribute', 'AttributeController');
            //////// Attribute Set //////////
            Route::resource('AttributeSet', 'AttributeSetController');
            //////// Attribute Value //////////
            Route::resource('AttributeValue', 'AttributeValueController');
            //////// TAX //////////
            Route::resource('Tax', 'TaxController');
            //////// Currency //////////
            Route::resource('Currency', 'CurrencyController');
            //////// Carrier //////////
            Route::resource('Carrier', 'CarrierController');
            //////// USER //////////
            Route::resource('Users', 'UsersController');
            //////// Roles //////////
            Route::resource('Roles', 'RolesApiController');
            //////// Permissions //////////
            Route::resource('Permissions', 'PermissionsApiController');
            //////// Orders //////////
            Route::resource('Orders', 'OrdersController');
            //////// City //////////
            Route::resource('City', 'CityController');
        });
        //
    });
});

//	############# END  API  Admin ###################################

//	############# API FOR User ###################################

Route::group(['namespace' => 'API', 'prefix' => 'user'], function () {
    //////// Category//////////
    Route::apiResource('Category', 'CategoryController');
    //////// Product //////////
    Route::apiResource('Product', 'ProductController');
    //////// Currency //////////
    Route::apiResource('Currency', 'CurrencyController');
    //////// City //////////
    Route::apiResource('City', 'CityController');


    Route::group(['middleware' => ['auth:api', 'role', 'cors', 'json.response']], function () {
        Route::middleware(['scope:Customer'])->group(function () {
            //==================== Profail ======================================================
            Route::apiResource('Users', 'UserController');
            Route::post('avatar', 'UserController@image_store');
            Route::apiResource('carts', 'CartController');
            Route::apiResource('orders', 'OrderController');
            Route::post('/carts/{cart}', 'CartController@addProducts');
            Route::post('/carts/{cart}/checkout', 'CartController@checkout');
            //////// Orders //////////


        });
        //
    });
});
