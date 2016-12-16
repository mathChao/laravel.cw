<?php

namespace App\BusinessModels;

use App\Models\Author as DbAuthor;
use Cache;
use DB;

class Author extends Model{
    private $id = null;

    public function __construct($id)
    {
        $this->id = $id;
        $cacheId = 'author-model-'.$id;
        $this->model = Cache::remember($cacheId, CACHE_TIME, function()use($id){
            return DbAuthor::find($id);
        });
    }

    public function getAuthorData()
    {
        $cacheId = 'author-data-'.$this->id;
        return Cache::remember($cacheId, CACHE_TIME, function(){
            $result = null;
            if($this->model){
                $sideTable = config('cwzg.edbPrefix').'author_data_'.$this->model->stb;
                $result = DB::table($sideTable)->where('id', $this->id)->get()->toArray()[0];
            }
            return $result;
        });
    }

    protected function asynLoad1(){
        $this->attributes = array_merge($this->attributes, $this->getAuthorData());
    }
}