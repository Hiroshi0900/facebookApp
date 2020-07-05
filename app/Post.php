<?php

namespace App;

use App\Scopes\ReverseScope;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	//
	protected $guarded = [];
	// boot（初期で絶対に走る処理）追加
	protected static function boot(){
		parent::boot();
		// TODO addGlobalScopeとは？？
		static::addGlobalScope(new ReverseScope);
	}
	public function likes(){
		return $this->belongsToMany(User::class,'likes','post_id','user_id');
	}
	public function comments(){
		return $this->hasMany(Comment::class);
	}
	public function user(){
		return $this->belongsTo(User::class);
	}
}
