<?php

namespace App\Http\Controllers;

use App\ModelHelpers\ArticleHelper;

class NewsController extends Controller{

    public function newsList($ttid = 0, $classid = 0){
        ArticleHelper::articleSearch([], 8, 1);
    }

    public function AjaxNewsList(){
        dd(123);
    }


    public function newsInfo($classid, $id){

    }

    public function AjaxNewsInfo(){

    }

}
