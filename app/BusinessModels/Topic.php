<?php

namespace App\BusinessModels;

use App\Services\Search\SystemArticleSearch;
use Cache;
use DB;

class Topic extends Model{
    private $search = null;
    private $dbPre = null;

    public function __construct($id)
    {
        $this->dbPre = config('cwzg.edbPrefix');
        $this->attributes['id'] = $id;
    }

    private function getTopicInfo(){
        $cacheId = 'topic-'.$this->attributes['id'];
        return  Cache::remember($cacheId, CACHE_TIME, function(){
            return DB::table($this->dbPre.'zt')->where('ztid',$this->attributes['id'])->first();
        });
    }

    public function asynLoad1(){
        $this->model = $this->getTopicInfo();
        $this->attributes['url'] = url('/'.$this->model->ztpath);
    }

    public function getArticleIds(){
        $cacheId = 'topic-article-id-'.$this->attributes['id'];
        return Cache::remember($cacheId, SHORT_CACHE_TIME, function(){
            return DB::table($this->dbPre.'enewsztinfo')
                ->where('ztid', $this->attributes['id'])
                ->select('id')
                ->get()
                ->keyBy('id')
                ->keys()
                ->toArray();
        });
    }

    public function getSearch(){
        if(!$this->search){
            $this->search = new SystemArticleSearch();
            $this->search->topic($this);
        }
        return $this->search;
    }

    public function resetSearch(SystemArticleSearch &$search){
        $search->resetAttribute();
        $search->topic($this);
    }
}