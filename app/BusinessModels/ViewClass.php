<?php

namespace App\BusinessModels;

use App\Services\Search\SystemArticleSearch;

class ViewClass extends Model{
    private $search = null;
    private static $_instance = null;

    public function __construct()
    {
        $this->attributes['name'] = '深度';
        $this->attributes['url'] = url('/view');
    }

    public function getSearch(){
        if(!$this->search){
            $config = [
                'isgood' => 5
            ];
            $this->search = new SystemArticleSearch($config);
        }
        return $this->search;
    }

    public static function getInstance(){
        if(!self::$_instance){
            self::$_instance = new ViewClass();
        }
        return self::$_instance;
    }
}