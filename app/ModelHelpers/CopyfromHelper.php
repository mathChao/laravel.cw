<?php

namespace App\ModelHelpers;

use App\Models\Copyfrom as DbCopyfrom;
use App\BusinessModels\Copyfrom as BusinessCopyfrom;

use DB;
use Cache;

class CopyfromHelper{

    private static $_instance = [];

    /**
     * @param $id
     * @param bool|false $reload
     * @return mixed
     */
    public static function getCopyfromInfo($id, $reload = false)
    {
        if( isset(self::$_instance[$id]) && $reload){
            unset(self::$_instance[$id]);
        }

        if( !isset(self::$_instance[$id]) ){
            self::$_instance[$id] = new BusinessCopyfrom($id);
        }

        return self::$_instance[$id];
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
        $ids = Cache::remember($cacheId, CACHE_TIME, function()use($filter, $pageRow, $page, $orderBy){
            $db = DB::table(config('cwzg.edbPrefix').'ecms_copyfrom');
            if($filter && is_array($filter)){
                foreach($filter as $key => $value){
                    if( strpos($key,  ' ')){
                        $arr = explode(' ', $key);
                        $db->where($arr[0], $arr[1], $value);
                    }else{
                        $db->where($key, $value);
                    }
                }
            }

            if($page && $pageRow){
                $db->limit($pageRow)->skip(($page-1)*$pageRow);
            }

            if($orderBy){
                $db->orderByRaw($orderBy);
            }

            return $db->select('id')->get()->keyBy('id')->keys()->toArray();
        });

        $copyfroms = [];
        foreach($ids as $id){
            $copyfroms[$id] = self::getCopyfromInfo($id);
        }

        return $copyfroms;
    }
}