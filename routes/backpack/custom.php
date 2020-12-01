<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
    'as' => 'admin.',
], function () { // custom admin routes
    Route::crud('announcement', 'AnnouncementCrudController');
    Route::crud('alert', 'AlertCrudController');
    Route::crud('resident', 'ResidentCrudController');
    Route::crud('barangay', 'BarangayCrudController');
    Route::crud('relative', 'RelativeCrudController');
    Route::crud('employee', 'EmployeeCrudController');
    Route::crud('evacuation', 'EvacuationCrudController');
    Route::crud('official', 'OfficialCrudController');

    Route::group(['prefix' => 'report' , 'as' => 'report.'], function(){
        Route::get('/generate', 'Report\GenerateReportController@index')->name('generate');
        Route::get('/weekly', 'Report\WeeklyAlertReportController@index')->name('weekly');
        Route::get('/monthly', 'Report\MonthlyReportController@index')->name('monthly');
        Route::get('/export/alert', 'Report\GenerateReportController@exportAlert')->name('export.alert');
    });

    // Custom Route for Alert
    Route::group(['prefix' => 'alert', 'as' => 'alert.'], function(){
        Route::patch('/{alert}/response', 'Custom\CustomAlertController@response')->name('response.update');
    });

    //Custom Route for Evacuation
    Route::group(['prefix' => 'evacuation', 'as' => 'evacuation.'], function(){
        Route::get('/barangay/list', 'Custom\CustomEvacuationController@index')->name('barangay.list');
        Route::get('/{evacuation}/userList', 'Custom\CustomEvacuationController@userList')->name('user.list');
        Route::get('/{evacuation}/unregisterUserList', 'Custom\CustomEvacuationController@unregisterUserList')->name('user.list.unregister');
        Route::post('/{evacuation}/adduser', 'Custom\CustomEvacuationController@addUser')->name('user.add');
        Route::post('/{evacuation}/addUnregisteredUser', 'Custom\CustomEvacuationController@addUnregisteredUser')->name('user.add.unregistered');
        Route::post('/{id}/removeunregisterUser', 'Custom\CustomEvacuationController@removeUnregisterUser')->name('user.remove.unregister');
        Route::post('/{user}/removeuser', 'Custom\CustomEvacuationController@removeUser')->name('user.remove');
    });

    // if not otherwise configured, setup the "my account" routes
    if (config('backpack.base.setup_my_account_routes')) {
        Route::get('edit-account-info', 'MyAccountController@getAccountInfoForm')->name('backpack.account.info');
        Route::post('edit-account-info', 'MyAccountController@postAccountInfoForm')->name('backpack.account.info.store');
        Route::post('change-password', 'MyAccountController@postChangePasswordForm')->name('backpack.account.password');
    }
}); // this should be the absolute last line of this file

Route::group(
[
    'namespace'  => 'App\Http\Controllers\Admin',
    'middleware' => config('backpack.base.web_middleware', 'web'),
    'prefix'     => config('backpack.base.route_prefix'),
],
function () {
    // if not otherwise configured, setup the "my account" routes
    if (config('backpack.base.setup_my_account_routes')) {
        Route::get('edit-account-info', 'MyAccountController@getAccountInfoForm')->name('backpack.account.info');
        Route::post('edit-account-info', 'MyAccountController@postAccountInfoForm')->name('backpack.account.info.store');
        Route::post('change-password', 'MyAccountController@postChangePasswordForm')->name('backpack.account.password');
    }
});
