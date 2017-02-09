<?php

namespace App\ModelHelpers;

use App\Models\Copyfrom as DbCopyfrom;
use App\BusinessModels\Copyfrom as BusinessCopyfrom;

use DB;
use Cache;
use App\ModelHelpers\Tools\DbSearch;

class CopyfromHelper{

    private static $_instance = [];
    private static $_dbSearch;

    /**
     * @param $id
     * @param bool|false $reload
     * @return mixed
     */
    public static function getCopyfrom($id, $reload = false)
    {
        if( isset(self::$_instance[$id]) && $reload){
            unset(self::$_instance[$id]);
        }

        if( !isset(self::$_instance[$id]) ){
            self::$_instance[$id] = new BusinessCopyfrom($id);
        }

        return self::$_instance[$id];
    }

    private static function getDbSearch(){
        if(self::$_dbSearch){
            self::$_dbSearch = new DbSearch('ecms_copyfrom');
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
    public static function copyfromSearch($filter = null, $pageRow = null, $page = 1, $orderBy = null){
        $cacheId = 'copyfrom-search-ids-'.cacheTagTransfer($filter).'-'.$pageRow.'-'.$page.'-'.($orderBy);
        $ids = Cache::remember($cacheId, SHORT_CACHE_TIME, function()use($filter, $pageRow, $page, $orderBy){
            $db = self::getDbSearch()->getSearchDb($filter,$pageRow, $page, $orderBy);
            return $db->select('id')->get()->keyBy('id')->keys()->toArray();
        });

        $copyfroms = [];
        foreach($ids as $id){
            $copyfroms[$id] = self::getCopyfrom($id);
        }

        return $copyfroms;
    }
}