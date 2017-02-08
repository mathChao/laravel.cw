<?php

namespace App\ModelHelpers;

use App\BusinessModels\Tag;
use DB;
use Cache;

class TagHelper{
    private static $_instance = [];

    public static function getTag($tagid){
        if(!isset(self::$_instance[$tagid])){
            self::$_instance[$tagid] = new Tag($tagid);
        }
        return self::$_instance[$tagid];
    }
}