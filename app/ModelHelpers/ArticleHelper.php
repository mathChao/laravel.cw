<?php

namespace App\ModelHelpers;

use App\Models\Article as DbArticle;
use App\BusinessModels\Article as BusinessArticle;

use DB;
use Cache;

class ArticleHelper{
    private static $_instance = [];

    /**
     * @param $id
     * @param bool|false $reload
     */
    public static function getArticle($id, $reload = false)
    {
        if( isset(self::$_instance[$id]) && $reload){
            unset(self::$_instance[$id]);
        }
        if( !isset(self::$_instance[$id]) ){
            self::$_instance[$id] = new BusinessArticle($id);
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
    public static function articleSearch($filter = null, $pageRow = null, $page = 1, $orderBy = 'newstime desc'){
        $cacheId = 'article-search-ids-'.cacheTagTransfer($filter).'-'.$pageRow.'-'.$page.'-'.cacheTagTransfer($orderBy);
        $ids = Cache::remember($cacheId, CACHE_TIME, function()use($filter, $pageRow, $page, $orderBy){
            $db = DB::table(config('cwzg.edbPrefix').'ecms_article');
            if($filter && is_array($filter)){
                foreach($filter as $key => $value){
                    if( strpos($key,  ' ')){
                        $arr = explode(' ', $key);
                        $field = $arr[0];
                        $op = $arr[1];
                        if($op == 'in'){
                            $db->whereIn($field, $value);
                        }else{
                            $db->where($field, $op, $value);
                        }

                    }else{
                        $db->where($key, $value);
                    }
                }
            }

            if($pageRow){
                $db->limit($pageRow);
            }

            if($page){
                $db->skip(($page-1)*$pageRow);
            }

            if($orderBy){
                $db->orderByRaw($orderBy);
            }

            return $db->select('id')->get()->keyBy('id')->keys()->toArray();
        });

        $articles = [];
        foreach($ids as $id){
            $articles[$id] = self::getArticle($id);
        }

        return $articles;
    }

    public static function getArticleCount($filter = null){
        $cacheId = 'article-count-'.cacheTagTransfer($filter);
        return Cache::remember($cacheId, CACHE_TIME, function()use($filter){
            $db = DB::table(config('cwzg.edbPrefix').'ecms_article');
            if($filter && is_array($filter)){
                foreach($filter as $key => $value){
                    if( strpos($key,  ' ')){
                        $arr = explode(' ', $key);
                        $field = $arr[0];
                        $op = $arr[1];
                        if($op == 'in'){
                            $db->whereIn($field, $value);
                        }else{
                            $db->where($field, $op, $value);
                        }

                    }else{
                        $db->where($key, $value);
                    }
                }
            }
            return $db->count();
        });
    }

    public static function getRandomArticle($limit = 10){
        $cacheId = 'article-200-id';
        $ids = Cache::remember($cacheId, CACHE_TIME, function(){
            return DB::table(config('cwzg.edbPrefix').'ecms_article')
                ->orderByRaw('onclick desc, newstime desc')
                ->limit('200')
                ->select('id')
                ->get()
                ->keyBy('id')
                ->keys()
                ->toArray();
        });

        $idsKeys = array_rand($ids, $limit);

        $articles = array();
        foreach($idsKeys as $key){
            $articles[] = self::getArticle($ids[$key]['id']);
        }

        return $articles;
    }
}