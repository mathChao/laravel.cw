<?php

namespace App\ModelHelpers;

use App\BusinessModels\Tag;
use DB;
use Cache;
use App\ModelHelpers\Tools\DbSearch;

class TagHelper{
    private static $_instance = [];
    private static $_dbSearch;

    public static function getTag($tagid){
        if(!isset(self::$_instance[$tagid])){
            self::$_instance[$tagid] = new Tag($tagid);
        }
        return self::$_instance[$tagid];
    }

    private static function getDbSearch(){
        if(self::$_dbSearch){
            self::$_dbSearch = new DbSearch('enewstags');
        }
        return self::$_dbSearch;
    }

    /**
     * @param null $filter
     * @param null $pageRow
     * @param int $page
     * @param null $orderBy
     * @return array
     */
    public static function tagSearch($filter = null, $pageRow = null, $page = 1, $orderBy = null){
        $cacheId = 'tag-search-ids-'.cacheTagTransfer($filter).'-'.$pageRow.'-'.$page.'-'.($orderBy);
        $tagids = Cache::remember($cacheId, SHORT_CACHE_TIME, function()use($filter, $pageRow, $page, $orderBy){
            $db = self::getDbSearch()->getSearchDb($filter,$pageRow, $page, $orderBy);
            return $db->select('tagid')->get()->keyBy('tagid')->keys()->toArray();
        });

        $tags = [];
        foreach($tagids as $tagid){
            $tags[$tagid] = self::getTag($tagid);
        }

        return $tags;
    }
}