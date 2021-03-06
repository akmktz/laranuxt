<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => 'auth:api'], function () {
    Route::prefix('posts')->group(function () {
        Route::get('', 'Posts\PostsController@index');
        Route::post('{item}/viewed', 'Posts\PostsController@viewed');
        Route::post('{item}/delete', 'Posts\PostsController@delete');
    });

    Route::prefix('sources')->group(function () {
        Route::get('', 'Posts\SourcesController@index');
        Route::post('/add', 'Posts\SourcesController@add');
        Route::post('{item}/save', 'Posts\SourcesController@save');
        Route::post('{item}/status', 'Posts\SourcesController@setStatus');
        Route::post('{item}/delete', 'Posts\SourcesController@delete');
    });

    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('oauth/{provider}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{provider}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

Route::get('/login', function () {
    abort(401, 'Unauthorized');
})->name('login');
