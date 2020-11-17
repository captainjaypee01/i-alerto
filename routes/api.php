<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['prefix' => 'mobile', 'namespace' => 'API'], function () {
    // Announcement
    Route::apiResource('announcements', 'AnnouncementController');

    // Login
    Route::apiResource('login', 'LoginController');
    Route::group(['prefix' => 'login_request'], function () {
        Route::post('/login_submit', 'LoginController@login_submit');
    });

    //Register
    Route::apiResource('register', 'RegisterController');

    Route::group(['prefix' => 'register/store'], function () {
        Route::post('/check_first', 'RegisterController@check_first');
        Route::post('/check_address','RegisterController@verify_address');
    });

    Route::group(['prefix' => 'update'], function () {
        Route::post('/edit/{id}', 'RegisterController@update');
        Route::post('/change_password/{id}', 'RegisterController@change_password');
    });

    //Alert
    Route::apiResource('alert','AlertController');
    Route::group(['prefix' => 'alerts'], function () {
        Route::get('/history', 'AlertController@history');
    });
    Route::group(['prefix' => 'alert'], function () {
        Route::post('/chat', 'AlertController@chat');
        Route::get('/conversations/{alert_id}','AlertController@conversations');
        Route::get('/conversation_status/{alert_id}/{user_id}/{role}','AlertController@conversation_status');
    });
});



