<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model{

    protected $primaryKey = 'plid';
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->table = config('cwzg.edbPrefix').'enewspl_1';
        parent::__construct($attributes);
    }

}