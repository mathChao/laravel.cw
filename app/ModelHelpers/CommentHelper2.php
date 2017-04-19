<?php

namespace App\ModelHelpers;

use DB;

class CommentHelper2{

    public static function updateCommentStatus($posts,$status,$time){
        if(count($posts) > 1){
            DB::table('pl_duoshuo')->whereIn('post_id',$posts)->update(['status' => $status,'updated_at' => $time]);
        }else{
            DB::table('pl_duoshuo')->where('post_id',$posts)->update(['status' => $status,'updated_at' => $time]);
        }

    }

    public static function deleteComments($posts){
        if(count($posts) > 1){
            DB::table('pl_duoshuo')->whereIn('post_id',$posts)->delete();
        }else{
            DB::table('pl_duoshuo')->where('post_id',$posts)->delete();
        }
    }

}