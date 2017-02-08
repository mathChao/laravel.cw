<?php

namespace App\ModelHelpers;

use App\BusinessModels\Topic;
use DB;
use Cache;

class TopicHelper{
    private static $_instance = [];

    public static function getTopic($ztid){
        if(!isset(self::$_instance[$ztid])){
            self::$_instance[$ztid] = new Topic($ztid);
        }
        return self::$_instance[$ztid];
    }
}