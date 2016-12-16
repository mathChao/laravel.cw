<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enewsclass extends Model{
    protected $primaryKey = 'classid';
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->table = config('cwzg.edbPrefix').'enewsclass';
        parent::__construct($attributes);
    }
}