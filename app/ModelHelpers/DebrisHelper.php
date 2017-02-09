<?php

namespace App\ModelHelpers;

use App\BusinessModels\Debris;
use Cache;
use App\ModelHelpers\Tools\DbSearch;

class DebrisHelper{
    private static $_instance = [];
    private static $_dbSearch;

    public static function getDebris($spid){
        if(!isset(self::$_instance[$spid])){
            self::$_instance[$spid] = new Debris($spid);
        }
        return self::$_instance[$spid];
    }

    private static function getDbSearch(){
        if(self::$_dbSearch){
            self::$_dbSearch = new DbSearch('enewssp');
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
    public static function debrisSearch($filter = null, $pageRow = null, $page = 1, $orderBy = null){
        $cacheId = 'debris-search-ids-'.cacheTagTransfer($filter).'-'.$pageRow.'-'.$page.'-'.($orderBy);
        $spids = Cache::remember($cacheId, SHORT_CACHE_TIME, function()use($filter, $pageRow, $page, $orderBy){
            $db = self::getDbSearch()->getSearchDb($filter,$pageRow, $page, $orderBy);
            return $db->select('spid')->get()->keyBy('spid')->keys()->toArray();
        });

        $debris = [];
        foreach($spids as $spid){
            $debris[$spid] = self::getDebris($spid);
        }

        return $debris;
    }
}