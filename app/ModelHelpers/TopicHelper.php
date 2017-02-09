<?php

namespace App\ModelHelpers;

use App\BusinessModels\Topic;
use DB;
use Cache;
use App\ModelHelpers\Tools\DbSearch;

class TopicHelper{
    private static $_instance = [];
    private static $_dbSearch;

    public static function getTopic($ztid){
        if(!isset(self::$_instance[$ztid])){
            self::$_instance[$ztid] = new Topic($ztid);
        }
        return self::$_instance[$ztid];
    }

    private static function getDbSearch(){
        if(self::$_dbSearch){
            self::$_dbSearch = new DbSearch('enewszt');
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
    public static function topicSearch($filter = null, $pageRow = null, $page = 1, $orderBy = null){
        $cacheId = 'topic-search-ids-'.cacheTagTransfer($filter).'-'.$pageRow.'-'.$page.'-'.($orderBy);
        $ztids = Cache::remember($cacheId, SHORT_CACHE_TIME, function()use($filter, $pageRow, $page, $orderBy){
            $db = self::getDbSearch()->getSearchDb($filter,$pageRow, $page, $orderBy);
            return $db->select('ztid')->get()->keyBy('ztid')->keys()->toArray();
        });

        $topics = [];
        foreach($ztids as $ztid){
            $topics[$ztid] = self::getTopic($ztid);
        }

        return $topics;
    }
}