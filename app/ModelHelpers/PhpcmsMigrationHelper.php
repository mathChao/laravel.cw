<?php

namespace App\ModelHelpers;

use App\Models\PhpcmsMigration;
use DB;


class PhpcmsMigrationHelper{

    public static function create($data){
        return PhpcmsMigration::create($data);
    }

    public static function getPhpcmsMigration($filter){
        return DB::table('phpcms_migration')->where($filter)->get();
    }

    public static function getPhpcmsMigrationCount($filter){
        return DB::table('phpcms_migration')->where($filter)->count();
    }

    public static function getNewIdFromOldId($type,$oldId){
        $result = DB::table('phpcms_migration')->where('phpcms_id',$oldId)->where('type',$type)->select('ecms_id')->first();
        if($result){
            return  $result->ecms_id;
        }
        return null;
    }

}