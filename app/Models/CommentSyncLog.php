<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentSyncLog extends Model{

    protected $primaryKey = 'id';
    protected $fillable = ['log_id', 'updatetime'];
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->table = config('cwzg.edbPrefix').'ecms_comments_sync_log';
        parent::__construct($attributes);
    }

}