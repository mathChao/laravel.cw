<?php

namespace App\ModelHelpers;

use DB;
use Cache;

class TagHelper{
    public static function getTagArticleIds($tag){
        $cacheId = 'tag-article-ids-'.md5($tag);
        return Cache::remember($cacheId, CACHE_TIME, function()use($tag){
            $prefix = config('cwzg.edbPrefix');
            $row = DB::table($prefix.'enewstags')
                ->select('tagid')
                ->where('tagname', $tag)
                ->first();

            $ids = [];
            if($row){
                $ids = DB::table($prefix.'enewstagsdata')
                    ->select('id')
                    ->where('tagid', $row->tagid)
                    ->get()
                    ->keyBy('id')
                    ->keys()
                    ->toArray();
            }
            return $ids;
        });
    }
}