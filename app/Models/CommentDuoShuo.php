<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentDuoShuo extends Model
{
	protected $fillable = ['comment', 'status','post_id','article_id'];
	public $timestamps = false;

	public function __construct(array $attributes = [])
	{
		$this->table = 'pl_duoshuo';
		parent::__construct($attributes);
	}
}
