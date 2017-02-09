<?php

namespace App\ModelHelpers\Tools;

use DB;

class DbSearch{
    private $db = null;

    public function __construct($table)
    {
        $this->db = DB::table(config('cwzg.edbPrefix').$table);
    }

    public function getSearchDb($filter, $pageRow, $page, $orderBy){
        if($filter && is_array($filter)){
            foreach($filter as $key => $value){
                if( strpos($key,  ' ')){
                    $arr = explode(' ', $key);
                    $field = $arr[0];
                    $op = $arr[1];
                    if($op == 'in'){
                        $this->db->whereIn($field, $value);
                    }else{
                        $this->db->where($field, $op, $value);
                    }

                }else{
                    $this->db->where($key, $value);
                }
            }
        }

        if($pageRow){
            $this->db->limit($pageRow);
        }

        if($page){
            $this->db->skip(($page-1)*$pageRow);
        }

        if($orderBy){
            $this->db->orderByRaw($orderBy);
        }

        return $this->db;
    }
}