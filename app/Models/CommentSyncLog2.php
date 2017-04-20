<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentSyncLog2 extends Model{

    protected $primaryKey = 'id';
    protected $table = 'comments_sync_log';
    protected $fillable = ['log_id', 'updatetime'];
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

}