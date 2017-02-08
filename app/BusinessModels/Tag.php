<?php

namespace App\BusinessModels;

use App\Services\Search\SystemArticleSearch;
use Cache;
use DB;

class Tag extends Model{
    private $search = null;
    private $dbPre = null;

    public function __construct($tagid)
    {
        $this->dbPre = config('cwzg.edbPrefix');
        $this->attributes['tagid'] = $tagid;
        $this->model = $this->getTagInfo();
        $this->attributes['url'] = url();
    }

    private function getTagInfo(){
        $cacheId = 'tag-'.$this->attributes['tagid'];
        return Cache::remember($cacheId, CACHE_TIME, function(){
            return DB::table($this->dbPre.'tags')->where('tagid', $this->attributes['tagid'])->first();
        });
    }

    public function getSearch(){
        if(!$this->search){
            $this->search = new SystemArticleSearch();
            $this->search->tag($this->tagname);
        }
        return $this->search;
    }
}