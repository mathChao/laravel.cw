<?php

namespace App\ModelHelpers;

use DB;

class CommentHelper{

    public static function updateCommentStatus($posts,$status){
        DB::table(config('cwzg.edbPrefix').'enewspl_1')->whereIn('post_id',$posts)->update(['checked' => $status]);
    }

    public static function deleteComments($posts){
        DB::table(config('cwzg.edbPrefix').'enewspl_1')->whereIn('post_id',$posts)->delete();
    }

}