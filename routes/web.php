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

Route::get('/', 'NewsController@newsList');

Route::get('/list/{ttid?}/{classid?}/', 'NewsController@newsList');

Route::get('/info/{id}/', 'NewsController@newsInfo');

Route::get('/home', 'HomeController@index');

Route::post('/ajax/list/load', 'NewsController@AjaxNewsListLoad');

Route::post('/ajax/news/mood/', 'NewsController@AjaxNewsMoodClick');

