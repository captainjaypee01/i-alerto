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

    
    Route::group(['prefix' => 'report' , 'as' => 'report.'], function(){
        Route::get('/generate', 'Report\GenerateReportController@index')->name('generate');
        Route::get('/weekly', 'Report\WeeklyAlertReportController@index')->name('weekly');
        Route::get('/export/alert', 'Report\GenerateReportController@exportAlert')->name('export.alert');
    });
    
}); // this should be the absolute last line of this file