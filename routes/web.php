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

Route::get('/', 'NewsController@index');
Route::get('/headline/', 'NewsController@headline');
Route::get('/thinktank/', 'NewsController@thinktank');
Route::get('/opinion/', 'NewsController@opinion');
Route::get('/view/', 'NewsController@view');
Route::get('/debate/', 'NewsController@debate');

Route::get('/politics/', 'NewsController@politics');
Route::get('/expose/', 'NewsController@expose');
Route::get('/theory/', 'NewsController@theory');
Route::get('/history/', 'NewsController@history');
Route::get('/photos/', 'NewsController@photos');

Route::get('/{type}/{time}/{id}.html', 'NewsController@newsInfo');

Route::post('/ajax/list/load', 'NewsController@AjaxNewsListLoad');
Route::post('/ajax/news/mood/', 'NewsController@AjaxNewsMoodClick');

