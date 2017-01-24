<?php

namespace App\BusinessModels;

use App\Models\Article as DbArticle;
use App\ModelHelpers\ArticleHelper;
use App\ModelHelpers\TagHelper;

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
                $result['newstext'] = $this->articleNewstextHandler($result['newstext']);
            }
            return $result;
        });
    }

    public function getRelatedArticle(){
        $ids = [];
        $keywords = array_explode([',', '，', ' ', '　'], $this->model->keyboard);
        foreach($keywords as $keyword){
            $ids = array_merge($ids, TagHelper::getTagArticleIds(strtoupper($keyword)));
        }

        $ids = array_unique($ids);
        $filter = [
            'id in' => $ids,
            'id != ' => $this->model->id,
        ];
        return ArticleHelper::articleSearch($filter, 3, null, 'newstime desc');
    }

    public function getArticleMood(){
        $cacheId = 'article-mood-'.$this->id;
        return Cache::remember($cacheId, CACHE_TIME, function(){
            $results = DB::table(config('cwzg.edbPrefix').'ecmsextend_mood')->where('id', $this->id)->get();
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

    public function getArticleMoodConfig(){
        $height = 44;
        $moodsConfig = config('cwzg.mood');
        $mood = $this->getArticleMood();
        $max = max($mood);
        foreach($moodsConfig as $key => &$config){
            $config['mood'] = $mood[$key];
            $config['height'] = $max ? $height * ($mood[$key]/$max) : 0;
        }
        return $moodsConfig;
    }

    public function articleNewstextHandler($newstext){
        $newtext = clearImageSizeSet($newstext);
        $newtext = textImageHandler($newtext, 'urlImg', ['220x128']);
        return $newtext;
    }

    protected function asynLoad1(){
        $classMap = config('cwzg.classMap');
        $classUrlName = isset($classMap[$this->model->classid]) ? $classMap[$this->model->classid]['name_en'].'/' : '';
        $this->attributes['url'] = '/'.$classUrlName.(date('Ym')).'/'.$this->model->id.'.html';
    }

    protected function asynLoad2(){
        $this->attributes = array_merge($this->attributes, $this->getArticleData(), $this->getArticleMood());
    }

}