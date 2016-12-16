<?php

namespace App\BusinessModels;

use App\Models\Article as DbArticle;
use App\ModelHelpers\ArticleHelper;

use Cache;
use DB;

class Article extends Model{
    private $id = null;

    public function __construct($id)
    {
        $this->id = $id;
        $cacheId = 'article-model-'.$id;
        $this->model = Cache::remember($cacheId, CACHE_TIME, function()use($id){
            return DbArticle::find($id);
        });
    }

    public function getArticleData()
    {
        $cacheId = 'article-data-'.$this->id;
        return Cache::remember($cacheId, CACHE_TIME, function(){
            $result = [];
            if($this->model){
                $sideTable = config('cwzg.edbPrefix').'ecms_article_data_'.$this->model->stb;
                $result = (array) DB::table($sideTable)->where('id', $this->id)->get()->toArray()[0];
            }
            return $result;
        });
    }

    public function getRelatedArticle(){
        $ids = [];
        $keywords = explode(',', $this->model->keyboard);
        foreach($keywords as $keyword){
            $cacheId = getKeywordsCacheId($keyword);
            if($keyword && Cache::has($cacheId)){
                $ids = array_merge($ids, Cache::get($cacheId));
            }
        }

        $ids = array_unique($ids);
        $filter = [
            'id in' => $ids,
            'id != ' => $this->model->id,
        ];
        return ArticleHelper::articleSearch($filter, 3, null, 'newstime desc');
    }

    protected function asynLoad1(){
        $this->attributes['url'] = '/info/'.$this->model->id;
        $this->attributes = array_merge($this->attributes, $this->getArticleData());
    }

}