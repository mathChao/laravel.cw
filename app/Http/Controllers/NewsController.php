<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ModelHelpers\ArticleHelper;
use App\ModelHelpers\EnewsclassHelper;

use App\Tools\Ajax;

use DB;
use Session;

class NewsController extends Controller{
    //首页
    public function index(){
        $filter = ['classid in' => [2,3,4,5,6]];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        $topArticles = ArticleHelper::articleSearch(['firsttitle >'=>0], 1, null);
        return view('list', [
            'key' => 'index',
            'articles' => $articles,
            'topArticle' => !empty($topArticles) ? current($topArticles) : null,
        ]);
    }

    //头条
    public function headline(){
        $filter = [
            'classid in' => [2,3,4,5,6],
            'firsttitle >' => 0,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'key' => 'headline',
            'articles' => $articles,
        ]);
    }

    //智库
    public function thinktank(){
        $filter = [
            'classid in' => [2,3,4,5,6],
            'ttid' => 4,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'ttid' => 4,
            'key' => 'thinktank',
            'articles' => $articles,
        ]);
    }

    //时评
    public function opinion(){
        $filter = [
            'classid in' => [2,3,4,5,6],
            'ttid' => 3,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'ttid' => 3,
            'key' => 'opinion',
            'articles' => $articles,
        ]);
    }

    //争鸣
    public function debate(){
        $filter = [
            'classid in' => [2,3,4,5,6],
            'ttid' => 5,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'ttid' => 5,
            'key' => 'debate',
            'articles' => $articles,
        ]);
    }

    //深度
    public function view(){
        $filter = [
            'classid in' => [2,3,4,5,6],
            'isgood' => 5,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'key' => 'view',
            'articles' => $articles,
        ]);
    }

    //观风察俗
    public function politics(){
        $filter = [
            'classid' => 2,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'classid' => 2,
            'key' => 'politics',
            'articles' => $articles,
        ]);
    }

    //察言观行
    public function expose(){
        $filter = [
            'classid' => 3,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'classid' => 3,
            'key' => 'expose',
            'articles' => $articles,
        ]);
    }

    //洞幽察微
    public function theory(){
        $filter = [
            'classid' => 4,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'classid' => 4,
            'key' => 'theory',
            'articles' => $articles,
        ]);
    }

    //察古知今
    public function history(){
        $filter = [
            'classid' => 5,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'classid' => 5,
            'key' => 'history',
            'articles' => $articles,
        ]);
    }

    //察网图说
    public function photos(){
        $filter = [
            'classid' => 6,
        ];
        $pageRow = config('news.listNum');
        $articles = ArticleHelper::articleSearch($filter, $pageRow, 1);
        return view('list', [
            'classid' => 6,
            'key' => 'photos',
            'articles' => $articles,
        ]);
    }
    
    public function AjaxNewsListLoad(Request $request){

        $post = $request->all();

        if(!isset($post['ttid']) || !isset($post['classid']) || !isset($post['page']) || !isset($post['key'])){
            return Ajax::argumentsError('Missing required parameter!');
        }

        $filter = [];
        if( $post['classid']){
            $filter['classid'] = $post['classid'];
        }else{
            $filter['classid in'] = [2,3,4,5,6];
        }
        
        if($post['ttid']){
            $filter['ttid'] = $post['ttid'];
        }
        
        if( $post['key'] == 'headline'){
            $filter['firsttitle >'] = 0;
        }elseif( $post['key'] == 'view'){
            $filter['isgood'] = 5;
        }

        $pageRow = config('news.listNum');
        $articleCount = ArticleHelper::getArticleCount($filter);
        $pageCount = ceil($articleCount/$pageRow);
        
        if($post['page'] > $pageCount){
            return Ajax::dataEnd('没有更多文章了');
        }
        $articles = ArticleHelper::articleSearch($filter, $pageRow, $post['page']);

        foreach($articles as &$article){
            $article->titlepic = urlImg('131x87', $article->titlepic);
            $article = $article->toArray();
        }

        return Ajax::success([
            'articles' => $articles,
        ]);
    }

    public function newsInfo($type,$time,$id){
        $article = ArticleHelper::getArticleInfo($id);
        if($article->isEmpty()){
            return 'article not exist';
        }

        $class = EnewsclassHelper::getClassInfo($article->classid);

        return view('content', [
            'article' => $article,
            'moodConfig' => $article->getArticleMoodConfig(),
            'related' => $article->getRelatedArticle(),
            'title' => $article->title.'-'.config('cwzg.sitename'),
            'keywords' => $article->keyboard,
            'description' => $article->smalltext,
            'class' => $class,
            'ttid' => Session::has('ttid') ? Session::get('ttid') : 12,
        ]);
    }

    public function AjaxNewsMoodClick(Request $request){
        $result = [
            'success' => false,
            'message' => ''
        ];
        if(!$request->mood || !$request->id){
            $result['message'] = 'data error';
            return Ajax::argumentsError($result);
        }

        return Ajax::success($result);
    }
}
