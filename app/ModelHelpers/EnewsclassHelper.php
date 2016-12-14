<?php

namespace App\ModelHelpers;

use App\Models\Enewsclass;
use DB;
use Cache;

class EnewsclassHelper{
    public static function classSearch($filter){
        return DB::table('phome_enewsclass')->where($filter)->get();
    }

    public static function getClassInfo($id){
        $cacheId = 'class-'.$id;
        return Cache::remember($cacheId, CACHE_TIME, function()use($id){
            return Enewsclass::find($id);
        });
    }
}