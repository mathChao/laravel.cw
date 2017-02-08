<?php

/**
 * Class SystemArticleSearch
 * 文章查询类，满足系统不同层次对文章的查询，包括
 * 文章本身字段的查询，id, classid, istop, isgood, firsttitle, ttid, ispic, author,
 * 绑定的查询：栏目，作者，专题，tag，碎片，标题分类，
 */

namespace App\Services\Search;

use App\ModelHelpers\ArticleHelper;
use DB;

class SystemArticleSearch{
    private $dbPre = null;

    private $ids = [];
    private $classids = [];
    private $ttids = [];
    private $authors = [];
    private $istop = null;
    private $isgood = null;
    private $firsttitle = null;
    private $ispic = null;

    private $limit = 0;
    private $page = 0;
    private $skip = 0;
    private $orderby = null;

    private $db = null;
    private $articles = [];

    public function __construct($config = null)
    {
        $this->dbPre = config('cwzg.edbPrefix');
        if($config && is_array($config)){
            if(isset($config['classid'])){
                if(is_array($config['classid'])){
                    foreach($config['classid'] as $value){
                        $this->appendClassId($value);
                    }
                }else{
                    $this->appendClassId($config['classid']);
                }
            }

            if(isset($config['ttid'])){
                if(is_array($config['ttid'])){
                    foreach($config['ttid'] as $value){
                        $this->appendTtid($value);
                    }
                }else{
                    $this->appendTtid($config['ttid']);
                }
            }

            if(isset($config['authors'])){
                if(is_array($config['authors'])){
                    foreach($config['authors'] as $value){
                        $this->appendAuthor($value);
                    }
                }else{
                    $this->appendAuthor($config['authors']);
                }
            }

            if(isset($config['istop'])){
                $this->setIsTop($config['istop']);
            }

            if(isset($config['isgood'])){
                $this->setIsTop($config['isgood']);
            }

            if(isset($config['firsttitle'])){
                $this->setIsTop($config['firsttitle']);
            }

            if(isset($config['ispic'])){
                $this->setIsTop($config['ispic']);
            }
        }
    }

    public function setIsTop($istop){
        if($istop != $this->istop){
            $this->db = null;
            $this->articles = [];
        }
        $this->istop = $istop;
        return $this;
    }

    public function setIsGood($isGood){
        if($isGood != $this->isgood){
            $this->db = null;
            $this->articles = [];
        }
        $this->isgood = $isGood;
        return $this;
    }

    public function setFirstTitle($firsttitle){
        if($firsttitle != $this->firsttitle){
            $this->db = null;
            $this->articles = [];
        }
        $this->firsttitle = $firsttitle;
        return $this;
    }

    public function setIsPic($ispic){
        if($ispic != $this->ispic){
            $this->db = null;
            $this->articles = [];
        }
        $this->ispic = $ispic;
        return $this;
    }

    public function appendId($id){
        if(is_array($id)){
            $this->ids = array_unique(array_merge($this->ids, $id));
        }elseif(!in_array($id, $this->ids)){
            $this->ids[] = $id;
        }
        $this->db = null;
        $this->articles = [];
        return $this;
    }

    public function appendClassId($classId){
        if(!in_array($classId, $this->classids)){
            $this->classids[] = $classId;
            $this->db = null;
            $this->articles = [];
        }
        return $this;
    }

    public function appendTtid($ttid){
        if(!in_array($ttid, $this->ttids)){
            $this->ttids[] = $ttid;
            $this->db = null;
            $this->articles = [];
        }
    }

    public function appendAuthor($author){
        if(!in_array($author, $this->authors)){
            $this->authors[] = $author;
            $this->db = null;
            $this->articles = [];
        }
        return $this;
    }

    public function newsClass($classname){
        $classInfo = DB::table($this->dbPre.'enewclass')
            ->where('classname', $classname)
            ->select('classid')
            ->first();

        $this->appendClassId($classInfo->classId);
        return $this;
    }

    public function infoType($infotype){
        $infotypeInfo = DB::table($this->dbPre.'enewsinfotype')
            ->where('tname', $infotype)
            ->select('typeid')
            ->first();

        $this->appendTtid($infotypeInfo->typeid);
        return $this;
    }


    public function topic($topic){
        $topicInfo = DB::table($this->dbPre.'enewszt')
            ->where('ztname', $topic)
            ->select('ztid')
            ->first();

        $ids = DB::table($this->dbPre.'enewsztinfo')
            ->where('ztid', $topicInfo->ztid)
            ->select('id')
            ->get()
            ->keyBy('id')
            ->keys()
            ->toArray();
        $this->appendId($ids);

        return $this;
    }

    public function tag($tag){
        $tagInfo = DB::table($this->dbPre.'enewstags')
            ->select('tagid')
            ->where('tagname', $tag)
            ->first();

        $ids = DB::table($this->dbPre.'newstagsdata')
            ->select('id')
            ->where('tagid', $tagInfo->tagid)
            ->get()
            ->keyBy('id')
            ->keys()
            ->toArray();
        $this->appendId($ids);

        return $this;
    }

    public function debris($debris){
        $debrisInfo = DB::table($this->dbPre.'newssp')
            ->select(['spid','sptype'])
            ->where('zpname', $debris)
            ->first();

        $ids = DB::table($this->dbPre.'enewssp_'.$debrisInfo->sptype)
            ->where('spid', $debrisInfo->spid)
            ->select('id')
            ->get()
            ->keyBy('id')
            ->keys()
            ->toArray();
        $this->appendId($ids);
        return $this;
    }

    public function limit($limit){
        if($limit != $this->limit){
            $this->db = null;
            $this->articles = [];
        }
        $this->limit = $limit;
        return $this;
    }

    public function page($page){
        if($page != $this->page){
            $this->db = null;
            $this->articles = [];
        }
        $this->page = $page;
        return $this;
    }

    public function skip($skip){
        if($skip != $this->skip){
            $this->db = null;
            $this->articles = [];
        }
        $this->skip = $skip;
        return $this;
    }

    public function orderby($orderby){
        if($orderby != $this->orderby){
            $this->db = null;
            $this->articles = [];
        }
        $this->orderby = $orderby;
        return $this;
    }

    private function getSearchDb(){
        if(!$this->db){
            $this->db = DB::table($this->dbPre.'ecms_article');

            if(!empty($this->ids)){
                $this->db->whereIn('id', $this->ids);
            }

            if(!empty($this->classids)){
                $this->db->whereIn('classid', $this->classids);
            }

            if(!empty($this->ttids)){
                $this->db->whereIn('ttid', $this->ttids);
            }

            if(!empty($this->authors)){
                $this->db->whereIn('author', $this->authors);
            }

            if($this->ispic !== null){
                $this->db->where('ispic', $this->ispic);
            }

            if($this->istop !== null){
                if(strpos($this->istop,' ') === false){
                    $this->db->where('istop', $this->istop);
                }else{
                    list($op,$value) = explode(' ', $this->istop);
                    $this->db->where('istop', $op, $value);
                }
            }

            if($this->isgood !== null){
                if(strpos($this->isgood,' ') === false){
                    $this->db->where('isgood', $this->isgood);
                }else{
                    list($op,$value) = explode(' ', $this->isgood);
                    $this->db->where('isgood', $op, $value);
                }
            }

            if($this->firsttitle !== null){
                if(strpos($this->firsttitle,' ') === false){
                    $this->db->where('firsttitle', $this->firsttitle);
                }else{
                    list($op,$value) = explode(' ', $this->firsttitle);
                    $this->db->where('firsttitle', $op, $value);
                }
            }

            if($this->orderby){
                $this->db->orderByRaw($this->orderby);
            }

            if($this->limit){
                $this->db->limit($this->limit);
            }

            if($this->limit && $this->page && !$this->skip){
                $this->skip(($this->page-1)*$this->limit);
            }

            if($this->skip){
                $this->db->skip($this->skip);
            }

        }
        return $this->db;
    }

    public function get(){
        if(empty($this->articles)){
            $db = $this->getSearchDb();
            $ids = $db->select('id')->get()->keyBy('id')->keys()->toArray();
            foreach($ids as $id){
                $this->articles[$id] = ArticleHelper::getArticle($id);
            }
        }
        return $this->articles;
    }

    public function count(){
       return $this->getSearchDb()->count();
    }

    public function sum($column){
        return $this->getSearchDb()->sum($column);
    }

    public function avg($column){
        return $this->getSearchDb()->average($column);
    }
}