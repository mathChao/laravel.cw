<?php

function cacheTagTransfer($var){
    if(is_array($var) || is_object($var)){
        return json_encode($var);
    }elseif(is_string($var)){
        return str_replace(' ', '-', $var);
    }else{
        return $var;
    }
}

function getArticlePagination($url, $page, $pageCount){
    $pagination = [
        'page' => $page,
        'pageCount' => $pageCount,
        'pre' => null,
        'next' => null,
        'full' => null,
    ];

    if( $page > 1 ){
        $pagination['pre'] = str_replace('%d', $page - 1, $url);
    }else{
        $pagination['pre'] = str_replace('%d', 1, $url);
    }

    if( $page < $pageCount ){
        $pagination['next'] = str_replace('%d', $page + 1, $url);
    }else{
        $pagination['next'] = str_replace('%d', $pageCount, $url);
    }

    $pagination['full'] = str_replace('%d', $page, $url).'&remains=1';

    return $pagination;
}

function getKeywordsCacheId($keywords){
    return 'article-keywords-'.md5($keywords);
}

function clearImageSizeSet($str){
    $pattern = '/(<img.+?style=.+?)(width:\s*?\d{1,4}px;?)(.+?)(height:\s*?\d{1,4}px;?)/';
//    preg_match_all($pattern, $str, $match);
//    var_dump($match);
    return preg_replace($pattern, '$1$3', $str);
}

function imageAddPrefix($str, $prefix = null, $filePrefix = null){
    if($prefix || $filePrefix){
        $pattern = '/(http:.+?)?\/d\/file(\/uploadfile\/\d{4}\/\d{4}\/)(\w+?)\.(jpg|png|jpeg|gif|bmp)/';
        //preg_match_all($pattern, $str, $match);
        //dd($match);
        $str = preg_replace($pattern, $prefix.'$2'.$filePrefix.'$3.$4', $str);
    }
    return $str;
}

function explodea($delimiter, $string){
    if(is_string($delimiter)){
        return explode($delimiter, $string);
    }elseif(is_array($delimiter)){
        $d = current($delimiter);
        $string = str_replace($delimiter, $d, $string);
        return explode($d, $string);
    }
}