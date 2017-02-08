<?php

namespace App\BusinessModels;

class Model{

    protected $model = null;
    protected $attributes = [];
    protected $asynStatus = [
        false,
        false,
        false,
    ];

    public function __set($name, $value)
    {
        if(isset($this->model->{$name})){
            $this->model->{$name} = $value;
        }else{
            $this->attributes[$name] = $value;
        }
    }

    public function __get($name)
    {
        if($this->model && isset($this->model->{$name})){
            return $this->model->{$name};
        }elseif(isset($this->attributes[$name])){
            return $this->attributes[$name];
        }else{
            $i = 0;
            while($i < count($this->asynStatus)){
                if(!$this->asynStatus[$i]){
                    $method = 'asynLoad'.($i+1);
                    if(method_exists($this, $method)){
                        $this->{$method}();
                    }
                    $this->asynStatus[$i] = true;
                    if(isset($this->attributes[$name])){
                        return $this->attributes[$name];
                    }
                }
                $i++;
            }
        }
        return null;
    }

    public function isEmpty(){
        return (!$this->model) && empty($this->attributes);
    }

    public function loadAllData(){
        $i = 0;
        while($i < count($this->asynStatus)){
            if(!$this->asynStatus[$i]){
                $method = 'asynLoad'.($i+1);
                if(method_exists($this, $method)){
                    $this->{$method}();
                }
                $this->asynStatus[$i] = true;
            }
            $i++;
        }
    }

    public function toArray(){
        $this->loadAllData();
        return array_merge($this->model->toArray(), $this->attributes);
    }
}