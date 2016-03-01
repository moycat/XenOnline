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

// APIs that needn't to check user

Route::group(['prefix' => 'api'], function () {
    Route::get('getProblemList', 'ProblemController@apiList');
    Route::get('getSolutionList', 'SolutionController@apiList');
});


Route::group(['middleware' => 'web'], function () {

    // Authentication Routes...
    Route::get('login', 'Auth\AuthController@showLoginForm');
    Route::post('login', 'Auth\AuthController@login');
    Route::get('logout', 'Auth\AuthController@logout');

    // Registration Routes...
    Route::get('register', 'Auth\AuthController@showRegistrationForm');
    Route::post('register', 'Auth\AuthController@register');

    // Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
    Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\PasswordController@reset');

    // Public Pages
    Route::get('/', function () {
        return Response::theme('index');
    });
    Route::get('/problem', 'ProblemController@index');
    Route::get('/solution', 'SolutionController@index');
    Route::get('/discussion', 'DiscussionController@index');
    Route::get('/problem/{pid}', ['as' => 'problem', 'uses' => 'ProblemController@show']);
    Route::get('/discussion/{did}', ['as' => 'discussion', 'uses' => 'DiscussionController@show']);

    // Protected Pages
    Route::group(['middleware' => 'auth'], function () {
        Route::post('/problem/{pid}/submit', 'ProblemController@submit');
        Route::get('/solution/{sid}', ['as' => 'solution', 'uses' => 'SolutionController@show']);
    });

    // APIs
    Route::group(['prefix' => 'api'], function () {
        Route::get('login', 'UserController@ApiLogin');
        Route::get('register', 'UserController@ApiRegister');
    });

    Route::get('/home', 'HomeController@Index');
    Route::get('/test', function(){

    });

});
