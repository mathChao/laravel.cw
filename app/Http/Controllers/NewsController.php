<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ModelHelpers\ArticleHelper;
use App\ModelHelpers\EnewsclassHelper;

use App\Tools\Ajax;

use DB;
use Session;

class NewsController extends Controller{
    public function newsList($ttid = 12, $classid = 80){

        Session::set('ttid', $ttid);

        $class = null;
        if($classid != 80){
            $class = EnewsclassHelper::getClassInfo($classid);
            if($class->isEmpty()){
                return 'class error';
            }
        }

        $pageRow = config('news.listNum');
        $page = 1;
        $orderBy = 'newstime desc';

        $filter = [];
        if($ttid != 12){
            if($ttid == 34){
                $filter['firsttitle >'] = 0;
            }else{
                $filter['ttid'] = $ttid;
            }
        }

        if($classid != 80){
            $filter['classid'] = $classid;
        }

        $articles = ArticleHelper::articleSearch($filter, $pageRow, $page, $orderBy);

        if($ttid == 12){
            $classid = 80;
        }

        return view('list', [
            'articles' => $articles,
            'ttid' => $ttid,
            'classid' => $classid,
        ]);
    }

    public function AjaxNewsListLoad(Request $request){

        $post = $request->all();

        if(!isset($post['ttid']) || !isset($post['classid']) || !isset($post['page'])){
            return Ajax::argumentsError('Missing required parameter!');
        }


        $filter = [];
        if($post['ttid'] != '12'){
            if($post['ttid'] == 34){
                $filter['firsttitle >'] = 0;
            }else{
                $filter['ttid'] = $post['ttid'];
            }
        }

        if( $post['classid'] != 80 ){
            $filter['classid'] = $post['classid'];
        }

        $pageRow = config('news.listNum');
        $articleCount = ArticleHelper::getArticleCount($filter);
        $pageCount = ceil($articleCount/$pageRow);

        if($post['page'] > $pageCount){
            return Ajax::dataEnd('没有更多文章了');
        }

        $orderBy = 'newstime desc';
        $articles = ArticleHelper::articleSearch($filter, $pageRow, $post['page'], $orderBy);

        foreach($articles as &$article){
            $article = $article->toArray();
        }

        return Ajax::success([
            'articles' => $articles,
        ]);

    }

    public function newsInfo($id){
        $article = ArticleHelper::getArticleInfo($id);
        if($article->isEmpty()){
            return 'article not exist';
        }

        $class = EnewsclassHelper::getClassInfo($article->classid);

        clearImageSizeSet($article->newstext);

        return view('content', [
            'article' => $article,
            'related' => $article->getRelatedArticle(),
            'title' => $article->title.'-'.config('cwzg.sitename'),
            'keywords' => $article->keyboard,
            'class' => $class,
            'ttid' => Session::has('ttid') ? Session::get('ttid') : 12,
        ]);
    }
}
