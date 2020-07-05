<?php

namespace App\Http\Controllers;

use App\Friend;
use Illuminate\Http\Request;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;
use Intervention\Image\Facades\Image;
use App\Post;

class PostController extends Controller
{
	public function index(){
		// 全て取得する場合
		// return new PostCollection(Post::all());
		// ユーザーにフレンドがいるときいないときで処理変更
		// return new PostCollection(request()->user()->posts);
		$friends = Friend::friendships();
		if($friends->isEmpty()){
    		return new PostCollection(request()->user()->posts);
		}
		return new PostCollection(
			Post::whereIn('user_id',[
				$friends->pluck('user_id'),
				$friends->pluck('friend_id'),
			])
			->get()
		);
	}
	public function store(){
		$data = request()->validate([
			'body'  => '',
			'image' => '',
			'width' => '',
			'height' => '',
		]);
		if(isset($data['image'])){
			$image = $data['image']->store('post-images','public');
			Image::make($data['image'])
				->fit($data['width'],$data['height'])
				->save(storage_path('app/public/post-images/'.$data['image']->hashName()));
		}
		$post = request()->user()->posts()->create([
			'body'  => $data['body'],
			'image' => $image ?? null,
		]);

		// Resourceクラスを使う
		return new PostResource($post);
	}
}
