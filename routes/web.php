<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['prevent-back-history']], function(){
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', 'AuthController@login')->name('login');
        Route::post('signin', 'AuthController@signin')->name('signin');

        Route::get('forget-password', 'AuthController@forget_password')->name('forget.password');
        Route::post('password-forget', 'AuthController@password_forget')->name('password.forget');
        Route::get('reset-password/{string}', 'AuthController@reset_password')->name('reset.password');
        Route::post('recover-password', 'AuthController@recover_password')->name('recover.password');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');

        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
        
        /** Projects */ 
            Route::any('projects', 'ProjectController@index')->name('projects');
            Route::get('projects/create', 'ProjectController@create')->name('projects.create');
            Route::post('projects/insert', 'ProjectController@insert')->name('projects.insert');
            Route::get('projects/edit', 'ProjectController@edit')->name('projects.edit');
            Route::post('projects/update', 'ProjectController@update')->name('projects.update');
            Route::get('projects/view', 'ProjectController@view')->name('projects.view');
            Route::post('projects/change_status', 'ProjectController@change_status')->name('projects.change_status');
            Route::any('projects/milestone/{id?}', 'ProjectController@milestone')->name('projects.milestone');
        /** Projects */ 
            
        /** Milestone */ 
            Route::any('milestones', 'MilestoneController@index')->name('milestones');
            Route::any('milestones/create/{id?}', 'MilestoneController@create')->name('milestones.create');
            Route::post('milestones/insert', 'MilestoneController@insert')->name('milestones.insert');
            Route::get('milestones/view', 'MilestoneController@view')->name('milestones.view');
            Route::get('milestones/edit/{id?}', 'MilestoneController@edit')->name('milestones.edit');
            Route::PATCH('milestones/update', 'MilestoneController@update')->name('milestones.update');
            Route::post('milestones/change_status', 'MilestoneController@change_status')->name('milestones.change_status');
            Route::post('milestones/payment_change_status', 'MilestoneController@payment_change_status')->name('milestones.payment_change_status');
        /** Milestone */ 
    });
    Route::get("{path}", function(){ return redirect()->route('login'); })->where('path', '.+');
});
// dd('hi');