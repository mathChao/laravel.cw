<?php

namespace App\ModelHelpers;

use DB;

class CommentHelper{

    public static function updateCommentStatus($posts,$status){
        if(count($posts) > 1){
            DB::table(config('cwzg.edbPrefix').'enewspl_1')->whereIn('post_id',$posts)->update(['checked' => $status]);
        }else{
            DB::table(config('cwzg.edbPrefix').'enewspl_1')->where('post_id',$posts)->update(['checked' => $status]);
        }

    }

    public static function deleteComments($posts){
        if(count($posts) > 1){
            DB::table(config('cwzg.edbPrefix').'enewspl_1')->whereIn('post_id',$posts)->delete();
        }else{
            DB::table(config('cwzg.edbPrefix').'enewspl_1')->where('post_id',$posts)->delete();
        }
    }

    public static function updateCommentCount(){
        $prefix = config('cwzg.edbPrefix');
        $sql = 'update '.$prefix.'ecms_article ca INNER JOIN
                (select p.id,count(p.id) as num from '.$prefix.'ecms_article a INNER JOIN '.$prefix.'enewspl_1 p
                on a.id = p.id where p.checked =1  GROUP BY id) as cb
                on ca.id = cb.id set ca.plnum = cb.num;';
        DB::update($sql);
    }

}