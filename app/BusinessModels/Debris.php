<?php

namespace App\BusinessModels;

use App\Services\Search\SystemArticleSearch;
use Cache;
use DB;

class Debris extends Model{
    private $search = null;
    private $dbPre = null;

    public function __construct($spid)
    {
        $this->dbPre = config('cwzg.edbPrefix');
        $this->attributes['spid'] = $spid;
    }

    private function getDebrisInfo(){
        $cacheId = 'debris-'.$this->attributes['spid'];
        return  Cache::remember($cacheId, CACHE_TIME, function(){
            return DB::table($this->dbPre.'enewssp')->where('spid', $this->attributes['spid'])->first();
        });
    }

    public function getArticleIds(){
        $cacheId = 'debris-article-id-'.$this->attributes['spid'];
        return Cache::remember($cacheId, SHORT_CACHE_TIME, function(){
            return DB::table($this->dbPre.'enewssp_'.$this->sptype)
                ->where('spid', $this->spid)
                ->select('id')
                ->get()
                ->keyBy('id')
                ->keys()
                ->toArray();
        });
    }

    public function asynLoad1(){
        $this->model = $this->getDebrisInfo();
    }

    public function getSearch(){
        if(!$this->search){
            $this->search = new SystemArticleSearch();
            $this->search->debris($this);
        }
        return $this->search;
    }


    public function resetSearch(SystemArticleSearch &$search){
        $search->resetAttribute();
        $search->debris($this);
    }
}