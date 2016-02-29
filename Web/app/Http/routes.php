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

    Route::get('getProblemList', 'ProblemController@apiList');
    Route::get('getSolutionList', 'SolutionController@apiList');

});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/


