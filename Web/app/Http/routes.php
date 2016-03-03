<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'web'], function () {

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
    Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\PasswordController@reset');

    // Public Pages
    Route::get('/', function () {
        return view('index');
    });

    // Problems
    Route::get('/problem', 'ProblemController@index');
    Route::get('/problem/page/{page}', 'ProblemController@index');
    Route::get('/problem/{pid}', ['as' => 'problem', 'uses' => 'ProblemController@show']);

    // Solutions
    Route::get('/solution', 'SolutionController@index');

    // Discussions
    Route::get('/discussion', 'DiscussionController@index');
    Route::get('/discussion/{did}', ['as' => 'discussion', 'uses' => 'DiscussionController@show']);

    // Users
    Route::get('user', 'UserController@index');
    Route::post('user', 'UserController@login');
    Route::put('user', 'Auth\AuthController@register');
    //Route::get('register', 'UserController@ApiRegister');
    Route::get('logout', 'Auth\AuthController@logout');

    // Protected Pages
    Route::group(['middleware' => 'auth'], function () {
        Route::put('/solution', 'SolutionController@submit');
        Route::get('/solution/{sid}', ['as' => 'solution', 'uses' => 'SolutionController@show']);
    });

});
