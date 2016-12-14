<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function(){
    return view('welcome');
});

Route::get('/list/{ttid?}/{classid?}/', 'NewsController@newsList');

Route::get('/info/{classid}/{id}/', 'NewsController@newsInfo');

Route::get('/ajax/newsList/', 'NewsController@AjaxNewsList');

Route::get('/ajax/newsInfo/', 'NewsController@AjaxNewsInfo');
