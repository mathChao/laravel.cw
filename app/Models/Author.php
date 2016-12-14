<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model{

    protected $primaryKey = 'id';
    public $timestamps = false;

    public function __construct(array $attributes)
    {
        $this->table = config('cwzg.edbPrefix').'ecms_author';
        parent::__construct($attributes);
    }
}