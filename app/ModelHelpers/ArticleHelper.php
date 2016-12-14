<?php

namespace App\ModelHelpers;

use App\Models\Article as DbArticle;
use App\Business\Article as BusinessArticle;

use DB;
use Cache;

class ArticleHelper{
    private static $_instance = [];

    public function __construct()
    {
        $this->_articleTable = config('cwzg.edbPrefix').'ecms_article';
    }

    /**
     * @param $id
     * @param bool|false $reload
     */
    public static function getArticleInfo($id, $reload = false)
    {
        if( isset(self::$_instance[$id]) && $reload){
            unset(self::$_instance[$id]);
        }
        if( !isset(self::$_instance[$id]) ){
            self::$_instance[$id] = new BusinessArticle($id);
        }

        return self::$_instance[$id];
    }

    public static function articleSearch($filter = null, $pageRow = null, $page = 1, $orderBy = null){
        $cacheId = 'article-search-aids-'.json_encode($filter).'-'.$pageRow.'-'.$page.'-'.$orderBy;
        $ids = Cache::remember($cacheId, CACHE_TIME, function()use($filter, $pageRow, $page, $orderBy){
            $db = DB::table(config('cwzg.edbPrefix').'ecms_article');
            if($filter && is_array($filter)){
                $db->where($filter);
            }

            if($page && $pageRow){
                $db->limit($pageRow)->skip(($page-1)*$pageRow);
            }

            if($orderBy){
                $db->orderByRaw($orderBy);
            }

            return $db->select('id')->get()->keyBy('id')->keys()->toArray();
        });

        $articles = [];
        foreach($ids as $id){
            $articles[$id] = self::getArticleInfo($id);
        }

        return $articles;
    }
}