<?php

function cacheTagTransfer($var){
    if(is_array($var) || is_object($var)){
        return md5(json_encode($var));
    }elseif(is_string($var)){
        return str_replace(' ', '-', $var);
    }else{
        return $var;
    }
}

function getKeywordsCacheId($keywords){
    return 'article-keywords-'.md5($keywords);
}

function clearImageSizeSet($str){
    $pattern = '/(<img.+?style=.+?)(width:\s*?\d{1,4}px;?)(.+?)(height:\s*?\d{1,4}px;?)/';
    return preg_replace($pattern, '$1$3', $str);
}

function textImageHandler($str, $callback = null, $arguments = []){
    $pattern = '/<img.+?src=([\'|\"])(.+?)\1/i';
    if(is_callable($callback) && preg_match_all($pattern, $str, $match) && isset($match[2])){
        foreach($match[2] as $value){
            $arg = $arguments;
            $arg[] = $value;
            $find = $value;
            $replace = call_user_func_array($callback, $arg);
            $str = str_replace($find, $replace, $str);
        }
    }
    return $str;
}

function array_explode($delimiter, $string){
    if(is_string($delimiter)){
        return explode($delimiter, $string);
    }elseif(is_array($delimiter)){
        $d = current($delimiter);
        $string = str_replace($delimiter, $d, $string);
        return explode($d, $string);
    }
}

function array_column_list($array, $column){
    $return = [];
    foreach($array as $value){
        if(is_array($value)){
            if(isset($value[$column])){
                $return[] = $value[$column];
            }
        }elseif(is_object($value)){
            if(isset($value->{$column})){
                $return[] = $value[$column];
            }
        }
    }
    return $return;
}

function urlImg($size, $name, $type = 1, $quality = 90)
{
    if (!isset ($size)) {
        return false;
    }

    if(empty($name)){
        $name = Config::get('image.default_pic');
    }

    $rate = Config::get('image.default_rate');

    list($width,$height) = explode('x',$size);

    $preset = [
        'width'   => floor((int)$width * $rate),
        'height'  => floor((int)$height * $rate),
        'type'    => (int)$type,
        'quality' => (int)$quality,
    ];

    $url =  app('Image')->getUrl($preset, $name);

    return $url;
}

function getfirstchar($s0){   //获取单个汉字拼音首字母。注意:此处不要纠结。汉字拼音是没有以U和V开头的

    $special = ['玥'=>'Y'];
    if(isset($special[$s0])){
        return $special[$s0];
    }

    $fchar = ord($s0{0});
    if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
    $s1 = iconv("UTF-8","gb2312", $s0);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $s0){$s = $s1;}else{$s = $s0;}
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if($asc >= -20319 and $asc <= -20284) return "A";
    if($asc >= -20283 and $asc <= -19776) return "B";
    if($asc >= -19775 and $asc <= -19219) return "C";
    if($asc >= -19218 and $asc <= -18711) return "D";
    if($asc >= -18710 and $asc <= -18527) return "E";
    if($asc >= -18526 and $asc <= -18240) return "F";
    if($asc >= -18239 and $asc <= -17923) return "G";
    if($asc >= -17922 and $asc <= -17418) return "H";
    if($asc >= -17922 and $asc <= -17418) return "I";
    if($asc >= -17417 and $asc <= -16475) return "J";
    if($asc >= -16474 and $asc <= -16213) return "K";
    if($asc >= -16212 and $asc <= -15641) return "L";
    if($asc >= -15640 and $asc <= -15166) return "M";
    if($asc >= -15165 and $asc <= -14923) return "N";
    if($asc >= -14922 and $asc <= -14915) return "O";
    if($asc >= -14914 and $asc <= -14631) return "P";
    if($asc >= -14630 and $asc <= -14150) return "Q";
    if($asc >= -14149 and $asc <= -14091) return "R";
    if($asc >= -14090 and $asc <= -13319) return "S";
    if($asc >= -13318 and $asc <= -12839) return "T";
    if($asc >= -12838 and $asc <= -12557) return "W";
    if($asc >= -12556 and $asc <= -11848) return "X";
    if($asc >= -11847 and $asc <= -11056) return "Y";
    if($asc >= -11055 and $asc <= -10247) return "Z";
    return NULL;
}

function pinyin($zh){
    $ret = '';
    for($i = 0; $i < mb_strlen($zh); $i++){
        $ret .= getfirstchar(mb_substr($zh, $i, 1));
    }
    return $ret;
}