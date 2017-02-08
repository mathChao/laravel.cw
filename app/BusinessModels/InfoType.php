<?php

namespace App\BusinessModels;

use App\Services\Search\SystemArticleSearch;
use Cache;
use DB;

class InfoType extends Model{
    private $search = null;
    private $dbPre = null;

    public function __construct($id)
    {
        $this->dbPre = config('cwzg.edbPrefix');
        $this->attributes['typeid'] = $id;
    }

    private function getTypeInfo(){
        $cacheId = 'infotype-'.$this->attributes['typeid'];
        return Cache::remember($cacheId, CACHE_TIME, function(){
            return DB::table($this->dbPre.'enewsinfotype')->where('typeid', $this->attributes['typeid'])->first();
        });
    }

    public function asynLoad1(){
        $this->model = $this->getTypeInfo();
        $this->attributes['url'] = url('/'.$this->model->tpath);
    }

    public function getSearch(){
        if(!$this->search){
            $this->search = new SystemArticleSearch();
            $this->search->appendTtid($this->attributes['typeid']);
        }
        return $this->search;
    }
}