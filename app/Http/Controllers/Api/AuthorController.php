<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Tools\Ajax;

use DB;


class AuthorController extends Controller{
    function getAuthorCountInfo(Request $request){
        $post = $request->all();

        if(!isset($post['author'])){
            return Ajax::argumentsError('author is required!');
        }

        $dbPrefix = config('cwzg.edbPrefix');

        $row = DB::table($dbPrefix.'ecms_article')
            ->select(['plnum', 'onclick', 'diggtop'])
            ->where('author', $post['author'])
            ->get();
        $result = [
            'articleCount' => $row->count(),
            'sumPlnum' => $row->sum('plnum'),
            'sumOnclick' => $row->sum('onclick'),
            'sumPraise' => $row->sum('diggtop'),
        ];
        return Ajax::success($result);

    }
}
