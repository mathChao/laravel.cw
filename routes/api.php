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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group(['middleware'=>'throttle:60,1', 'namespace' => 'Api'], function(){
    Route::post('/article/hasDiggtop', 'ArticleController@getArticleHasDiggtop');
    Route::post('/author/countInfo', 'AuthorController@getAuthorCountInfo');
    Route::post('/comment/sync', 'CommentController@syncCallback');
    Route::get('/article/addClick/{cid}/{id}', 'ArticleController@addClick');
});


