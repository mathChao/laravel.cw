<?php

namespace App\ModelHelpers;

use App\Models\Author as DbAuthor;
use App\BusinessModels\Author as BusinessAuthor;
use App\ModelHelpers\Tools\DbSearch;

use DB;
use Cache;

class AuthorHelper{
    private static $_instance = [];
    private static $_dbSearch;
    /**
     * @param $id
     * @param bool|false $reload
     * @return mixed
     */
    public static function getAuthor($id, $reload = false)
    {
        if( isset(self::$_instance[$id]) && $reload){
            unset(self::$_instance[$id]);
        }
        if( !isset(self::$_instance[$id]) ){
            self::$_instance[$id] = new BusinessAuthor($id);
        }

        return self::$_instance[$id];
    }

    private static function getDbSearch(){
        if(self::$_dbSearch){
            self::$_dbSearch = new DbSearch('ecms_author');
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
    public static function authorSearch($filter = null, $pageRow = null, $page = 1, $orderBy = null){
        $cacheId = 'author-search-ids-'.cacheTagTransfer($filter).'-'.$pageRow.'-'.$page.'-'.($orderBy);
        $ids = Cache::remember($cacheId, SHORT_CACHE_TIME, function()use($filter, $pageRow, $page, $orderBy){
            $db = self::getDbSearch()->getSearchDb($filter,$pageRow, $page, $orderBy);
            return $db->select('id')->get()->keyBy('id')->keys()->toArray();
        });

        $authors = [];
        foreach($ids as $id){
            $authors[$id] = self::getAuthor($id);
        }

        return $authors;
    }

    public static function getAuthorColumnList($column = 'title'){
        $cacheId = 'author-name-list-'.$column;
        return Cache::remember($cacheId, LONG_CACHE_TIME, function()use($column){
            return array_column_list(self::authorSearch(), $column);
        });
    }
}