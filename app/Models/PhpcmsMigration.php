<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhpcmsMigration extends Model{
    protected $table = 'phpcms_migration';
    protected $primaryKey = 'id';

    protected $fillable = ['type', 'name', 'phpcms_table', 'phpcms_id', 'ecms_table', 'ecms_id'];

}