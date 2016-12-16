<?php

namespace App\ModelHelpers;

use App\Models\Enewsclass as DbEnewclass;
use App\BusinessModels\Enewsclass as BusinessEnewclass;

use DB;
use Cache;

class EnewsclassHelper{

    private static $_instance = [];

    /**
     * @param $id
     * @param bool|false $reload
     * @return mixed
     */
    public static function getClassInfo($classid, $reload = false){
        if( isset(self::$_instance[$classid]) && $reload){
            unset(self::$_instance[$classid]);
        }

        if( !isset(self::$_instance[$classid]) ){
            self::$_instance[$classid] = new BusinessEnewclass($classid);
        }

        return self::$_instance[$classid];
    }

    /**
     * @param null $filter
     * @param null $pageRow
     * @param int $page
     * @param null $orderBy
     * @return array
     */
    public static function enewsclassSearch($filter = null, $pageRow = null, $page = 1, $orderBy = null){
        $cacheId = 'enewsclass-search-classids-'.cacheTagTransfer($filter).'-'.$pageRow.'-'.$page.'-'.($orderBy);

        $classids = Cache::remember($cacheId, CACHE_TIME, function()use($filter, $pageRow, $page, $orderBy){
            $db = DB::table(config('cwzg.edbPrefix').'enewsclass');
            if($filter && is_array($filter)){
                $db->where($filter);
            }

            if($page && $pageRow){
                $db->limit($pageRow)->skip(($page-1)*$pageRow);
            }

            if($orderBy){
                $db->orderByRaw($orderBy);
            }

            return $db->select('classid')->get()->keyBy('classid')->keys()->toArray();
        });

        $classes = [];
        foreach($classids as $classid){
            $classes[$classid] = self::getClassInfo($classid);
        }

        return $classes;
    }

}