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
        /** Projects */ 
    });
    Route::get("{path}", function(){ return redirect()->route('login'); })->where('path', '.+');
});
// dd('hi');