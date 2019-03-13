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

Route::group(['namespace' => 'Api', 'middleware' => 'api'], function()
{
    Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router)
    {
        Route::post('/register', 'AuthController@register')->name('auth.register');
        Route::post('/login', 'AuthController@login')->name('auth.login');
        Route::get('/logout', 'AuthController@logout')->name('auth.logout');
    });

    Route::post('/posts/{post}/like', 'PostController@like')->name('posts.like');
    Route::post('/posts/{post}/dislike', 'PostController@dislike')->name('posts.dislike');
    Route::resource('posts', 'PostController')->only([
        'index', 'store', 'show'
    ]);

    Route::post('/posts/{post}/comments/{comment}', 'CommentController@store')->name('posts.comments.comments.store');
    Route::post('/posts/{post}/comments', 'CommentController@store')->name('posts.comments.store');
    Route::post('/posts/{post}/comments/{comment}/like', 'CommentController@like')->name('posts.comments.like');
    Route::post('/posts/{post}/comments/{comment}/dislike', 'CommentController@dislike')->name('posts.comments.dislike');
});
