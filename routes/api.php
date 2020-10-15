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

    Route::group(['prefix' => 'update'], function () {
        Route::post('/edit/{id}', 'RegisterController@update');
        Route::post('/change_password/{id}', 'RegisterController@change_password');
    });

    //Alert
    Route::apiResource('alert','AlertController');
    Route::group(['prefix' => 'alerts'], function () {
        Route::get('/history', 'AlertController@history');
    });
});



