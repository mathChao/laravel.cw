<?php

namespace App\BusinessModels;

use App\Services\Search\SystemArticleSearch;
use Cache;
use DB;

class Debris extends Model{
    private $search = null;
    private $dbPre = null;

    public function __construct($debris)
    {
        $this->dbPre = config('cwzg.edbPrefix');
        $this->attributes['debris'] = $debris;
    }

    private function getDebrisInfo(){
        $cacheId = 'debris-'.md5($this->debris);
        return  Cache::remember($cacheId, CACHE_TIME, function(){
            return DB::table($this->dbPre.'enewssp')->where('spname', $this->attributes['debris'])->first();
        });
    }

    public function asynLoad1(){
        $this->model = $this->getDebrisInfo();
    }

    public function getSearch(){
        if(!$this->search){
            $this->search = new SystemArticleSearch();
            $this->search->debris($this->attributes['debris']);
        }
        return $this->search;
    }
}