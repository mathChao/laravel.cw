<?php

namespace App\Services;

class Image
{

    private $server;
    private $key;

    public function __construct($host,$key){
        $this->server  = self::getServer($host);
        $this->key = $key;
    }

    public function getUrl($pre_set, $name)
    {
        if (empty($pre_set) or empty($name)) {
            return false;
        }

        $width   = isset($pre_set['width'])?$pre_set['width']:0;
        $height  = isset($pre_set['height'])?$pre_set['height']:0;
        $quality = isset($pre_set['quality'])?$pre_set['quality']:90;
        $type    = isset($pre_set['type'])? intval($pre_set['type']):1;

        if(self::startsWith($name,$this->server)){
            $name = str_replace($this->server.'/','',$name);
        }

        $signature = $this->gen_signature($width, $height, $quality, $type, $name);

        return $this->server . '/image/' . $width . "x" . $height . "/" . $quality . '/' . $type . '/' . $signature . '/' . $name;
    }

    private static function getServer($app_id)
    {
        if(self::startsWith($app_id,'http')){
            return $app_id;
        }else{
            return "http://" . $app_id;
        }
    }

    private function gen_signature($width, $height, $quality, $type, $name)
    {
        return substr(md5(md5($this->key) . $width . $height . $quality . $type . $name), 0, 16);
    }

    protected static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

}
