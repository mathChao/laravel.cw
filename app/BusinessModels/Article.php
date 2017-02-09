<?php

namespace App\BusinessModels;

use App\ModelHelpers\TopicHelper;
use App\Models\Article as DbArticle;
use App\ModelHelpers\ArticleHelper;
use App\ModelHelpers\TagHelper;
use App\ModelHelpers\EnewsclassHelper;
use App\ModelHelpers\AuthorHelper;
use App\Services\Search\SystemArticleSearch;

use Cache;
use DB;

class Article extends Model{
    private $id = null;
    private $dbPre = null;

    public function __construct($id)
    {
        $this->id = $id;
        $this->dbPre = config('cwzg.edbPrefix');
        $cacheId = 'article-model-'.$id;
        $this->model = Cache::remember($cacheId, CACHE_TIME, function()use($id){
            return DbArticle::find($id);
        });
    }

    private function getArticleData()
    {
        $cacheId = 'article-data-'.$this->id;
        return Cache::remember($cacheId, CACHE_TIME, function(){
            $result = [];
            if($this->model){
                $sideTable = $this->dbPre.'ecms_article_data_'.$this->model->stb;
                $result = (array) DB::table($sideTable)->where('id', $this->id)->get()->toArray()[0];
                $result['newstext'] = $this->articleNewstextHandler($result['newstext']);
            }
            return $result;
        });
    }

    private function articleNewstextHandler($newstext){
        $newstext = stripslashes($newstext);
        $newtext = clearImageSizeSet($newstext);
        $newtext = textImageHandler($newtext, 'urlImg', ['220x128']);
        return $newtext;
    }

    public function getArticleMood(){
        $cacheId = 'article-mood-'.$this->id;
        return Cache::remember($cacheId, CACHE_TIME, function(){
            $results = DB::table($this->dbPre.'ecmsextend_mood')->where('id', $this->id)->get();
            return [
                'mood1'=>$results->sum('mood1'),
                'mood2'=>$results->sum('mood2'),
                'mood3'=>$results->sum('mood3'),
                'mood4'=>$results->sum('mood4'),
                'mood5'=>$results->sum('mood5'),
                'mood6'=>$results->sum('mood6'),
                'mood7'=>$results->sum('mood7'),
                'mood8'=>$results->sum('mood8'),
                'mood9'=>$results->sum('mood9'),
                'mood10'=>$results->sum('mood10'),
                'mood11'=>$results->sum('mood11'),
                'mood12'=>$results->sum('mood12'),
            ];
        });
    }

    protected function asynLoad1(){
        $this->attributes = array_merge($this->attributes, $this->getArticleData(), $this->getArticleMood());
    }

    public function getArticleTags(){
        $cacheId = 'article-tag-id-'.$this->id;
        $tagIds = Cache::remember($cacheId, CACHE_TIME, function(){
            return DB::table($this->dbPre.'enews_tagsdata')
                ->where('id',$this->id)
                ->select('tagid')
                ->distinct()
                ->get()
                ->keyBy('tagid')
                ->keys()
                ->toArray();
        });

        $tags = [];
        foreach($tagIds as $tagId){
            $tags[$tagId] = TagHelper::getTag($tagId);
        }
        return $tags;
    }

    public function getArticleClass(){
        return EnewsclassHelper::getClassInfo($this->classid);
    }

    public function getArticleAuthor(){
        $authors = AuthorHelper::authorSearch(['title'=>$this->author]);
        return !empty($authors) ? $authors[0] : null;
    }

    //获取文章所属专题
    public function getArticleTopics(){
        $cacheId = 'article-topic-id-'.$this->id;
        $ztids = Cache::remember($cacheId, CACHE_TIME, function(){
            return DB::table($this->dbPre.'enewsztinfo')->where('id', $this->id)->select('ztid')->distinct()->get()->keyBy('ztid')->keys()->toArray();
        });

        $topics = [];
        foreach($ztids as $ztid){
            $topics[$ztid] = TopicHelper::getTopic($ztid);
        }

        return $topics;
    }

    public function getArticleTopic(){
        $topics = $this->getArticleTopics();
        return !empty($topics) ? $topics[0] : null;
    }

    public function getRelatedArticle($limit = 10){
        $search = new SystemArticleSearch();
        if($this->keyid){
            $ids = explode(',', $this->keyid);
            $search->appendId($ids);
            return $search->limit($limit)->orderby('newstime desc')->get();
        }else{
            $tags = $this->getArticleTags();
            if(!empty($tags)){
                foreach($tags as $tag){
                    $search->tag($tag->tagname);
                }
                return $search->limit($limit)->orderby('newstime desc')->get();
            }else{
                return ArticleHelper::getRandomArticle();
            }
        }
    }

}