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

}