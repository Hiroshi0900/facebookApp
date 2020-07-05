<?php

namespace App\Http\Controllers;

use App\Exceptions\FriendRequestNotFoundException;
use App\Friend;
use App\Http\Resources\Friend as ResourcesFriend;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FriendRequestResponseController extends Controller
{
	// データ取得
	public function store(){
		// TODO APIで取得するデータを定義して返すの？？
		$data = request()->validate([
			'user_id' => 'required',
			'status'  => 'required',
		]);
// var_dump($data);
// exit;
		// TODO userが飛んできたユーザーIDでフレンドがログインユーザーのID？？
		try {
		    $friendRequest = Friend::where('user_id' ,$data['user_id'])
		    		->where('friend_id',auth()->user()->id)
		    		->firstOrFail();
		} catch (ModelNotFoundException $e){
			throw new FriendRequestNotFoundException();
		}
		// TODO フレンドリクエストモデルに承認日の値をマージ
		$friendRequest->update(array_merge($data,[
			'confirmed_at' => now(),
		]));
		return new ResourcesFriend($friendRequest);
	}
	// データ削除
	public function destroy(){
		$data = request()->validate([
			'user_id' => 'required',
		]);
		try {
		    $friendRequest = Friend::where('user_id' ,$data['user_id'])
		    		->where('friend_id',auth()->user()->id)
					->firstOrFail()
					->delete();
		} catch (ModelNotFoundException $e){
			throw new FriendRequestNotFoundException();
		}
		return response()->json([],204);
	}
}
