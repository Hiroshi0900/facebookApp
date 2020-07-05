<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
	// TODO protecedって何してるの
	protected $guarded = [];
	protected $dates = ['confirmed_at'];

	// TODO ユーザー同士の連結したデータを取得？
	public static function friendship($userId){
		return (new static())
		    ->where(function($query) use ($userId) {
				return $query->where('user_id',auth()->user()->id)
				    ->where('friend_id',$userId);
			})
			// TODO 逆パターンも考慮
			->orWhere(function($query) use ($userId) {
				return $query->where('friend_id',auth()->user()->id)
				    ->where('user_id',$userId);
			})
			->first();
	}
	// TODO これ何のためにつくんの？？
	// 自分だけの投稿じゃなくて、フレンドの投稿を取得した
	public static function friendships(){
		return (new static())
			->whereNotNull('confirmed_at')
			->where(function($query){
				return $query->where('user_id',auth()->user()->id)
				    ->orWhere('friend_id',auth()->user()->id);
			})
			->get();
	}
}
