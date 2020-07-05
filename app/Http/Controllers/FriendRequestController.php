<?php

namespace App\Http\Controllers;


use App\Friend;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\Friend as FriendResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\ValidationErrorException;
use Illuminate\Validation\ValidationException;

class FriendRequestController extends Controller
{
    public function store(){
		
		$data = request()->validate([
			'friend_id' => 'required',
		]);
		// 例外処理を追加
		try {
		    User::findOrFail($data['friend_id'])
				// ->friends()->attach(auth()->user()); //重複データを考慮
				->friends()->syncWithoutDetaching(auth()->user());
		} catch (ModelNotFoundException $e){
			throw new UserNotFoundException();
		}
		return new FriendResource(
			Friend::where('user_id',auth()->user()->id)
				  ->where('friend_id',$data['friend_id'])
				  ->first()
		);
	}
}
