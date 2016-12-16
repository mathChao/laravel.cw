<?php

namespace App\BusinessModels;

use App\Models\Enewsclass as DbEnewsclass;
use Cache;

class Enewsclass extends Model{

    public function __construct($id)
    {
        $cacheId = 'enewclas-model-'.$id;
        $this->model = Cache::remember($cacheId, CACHE_TIME, function()use($id){
            return DbEnewsclass::find($id);
        });
    }

}