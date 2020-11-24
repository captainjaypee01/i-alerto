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
    

    Route::group(['prefix' => 'login/update'], function () {
        Route::post('/update_token','LoginController@update_token');
        Route::post('/remove_token','LoginController@remove_token');
    });

    //Register
    Route::apiResource('register', 'RegisterController');

    Route::group(['prefix' => 'register/email'], function () {
        Route::post('/resend_code','RegisterController@resend_code');
        Route::post('/verify_account','RegisterController@verify_account');
    });
    

    Route::group(['prefix' => 'register/store'], function () {
        Route::post('/check_first', 'RegisterController@check_first');
        Route::post('/check_address','RegisterController@verify_address');
    });

    Route::group(['prefix' => 'register/get'], function () {
        Route::get('/barangay','RegisterController@barangay');
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


    //Evacuation
    Route::apiResource('evacuation','EvacuationController');
    Route::group(['prefix' => 'evacuation'], function () {
        Route::post("/update_capacity","EvacuationController@update_capacity");

        Route::group(['prefix' => 'get'], function () {
            Route::get('evacuations','EvacuationController@evacuations');
        });
    });

    //Barangay
    Route::apiResource('barangay','BarangayController');
});



