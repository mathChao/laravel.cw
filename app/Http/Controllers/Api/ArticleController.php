<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Tools\Ajax;

use DB;


class ArticleController extends Controller{
    function getArticleHasDiggtop(Request $request){
        $post = $request->all();
        $dbPrefix = config('cwzg.edbPrefix');
        $row= DB::table($dbPrefix.'enewsdiggips')
            ->select('ips')
            ->where(['classid'=>$post['classid'], 'id'=>$post['id']])
            ->limit('1')
            ->first();
        $ips = '';

        if($row){
            $ips = $row->ips;
        }

        $result = 0;
        $ip = $request->ip();
        if(strpos($ips, $ip) !== false){
            $result = 1;
        }

        return Ajax::success($result);

    }
}
