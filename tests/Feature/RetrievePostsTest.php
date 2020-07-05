<?php

namespace Tests\Feature;

use App\Friend;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RetrievePostsTest extends TestCase
{
	use RefreshDatabase;

	public function test_user_retrieve_post_text()
    {
		// 例外処理を弾く
		// $this->withoutExceptionHandling();
		
		// TODO あとで何をやっていたのかを調べる
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		// 他のユーザーも取得する
		$anotherUser = factory(\App\User::class)->create();
		$posts = factory(\App\Post::class,2)->create(['user_id' => $anotherUser->id]);
		Friend::create([
			'user_id'      => $user->id,
			'friend_id'    => $anotherUser->id,
			'confirmed_at' => now(),
			'status'       => 1,
		]);
		
		// レスポンスを受け取る
		$response = $this->get('/api/posts');

		// アサートステータスを取得
		// アサートするデータの設定も入れる

		$response->assertStatus(200)
		    ->assertJson([
				'data' => [
					[
				        'data' => [
				        	'type'       => 'posts',
				        	'post_id'    => $posts->last()->id,
				        	'attributes' => [
								'body'      => $posts->last()->body,
								'image'     => url('storage/'.$posts->last()->image),
								'posted_at' => $posts->last()->created_at->diffForHumans(),
				        	]
					    ],
					],
					[
				        'data' => [
				        	'type'       => 'posts',
				        	'post_id'    => $posts->first()->id,
				        	'attributes' => [
								'body'      => $posts->first()->body,
								'image'     => url('storage/'.$posts->first()->image),
								'posted_at' => $posts->first()->created_at->diffForHumans(),
				        	]
					    ],
					],
				],
				'links' =>[
					'self' => url('/posts'),
				]
				
			]);
	}
	public function test_user_retrieve_post_text_re()
    {
		// 例外処理を弾く
		// $this->withoutExceptionHandling();
		
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		$anotherUser = factory(\App\User::class)->create();
		$posts = factory(\App\Post::class,2)->create(['user_id' => $anotherUser->id]);
		// TODO フレンドのモデルを取得？？
		Friend::create([
			'user_id'      => $user->id,
			'friend_id'    => $anotherUser->id,
			'confirmed_at' => now(),
			'status'       => 1,
		]);

		// レスポンスを受け取る
		$response = $this->get('/api/posts');

		// アサートステータスを取得
		// アサートするデータの設定も入れる

		$response->assertStatus(200)
		    ->assertJson([
				'data' => [
					[
				        'data' => [
				        	'type'       => 'posts',
				        	'post_id'    => $posts->last()->id,
				        	'attributes' => [
								'body'      => $posts->last()->body,
								'image'     => url('storage/'.$posts->last()->image),
								'posted_at' => $posts->last()->created_at->diffForHumans(),
				        	]
					    ],
					],
					[
				        'data' => [
				        	'type'       => 'posts',
				        	'post_id'    => $posts->first()->id,
				        	'attributes' => [
								'body'      => $posts->first()->body,
								'image'     => url('storage/'.$posts->first()->image),
								'posted_at' => $posts->first()->created_at->diffForHumans(),
				        	]
					    ],
					],
				],
				'links' =>[
					'self' => url('/posts'),
				]
				
			]);
	}
	// ユーザーの投稿のみを取得する
	public function test_user_only_retrieve_post_text(){
		$this->withoutExceptionHandling();
		
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		$posts = factory(\App\Post::class,2)->create();
		
		// レスポンスを受け取る
		$response = $this->get('/api/posts');
		// TODO assertExactJsonとは何？？
		$response->assertStatus(200)
		         ->assertExactJson([
					 'data'  => [],
					 'links' => [
						 'self' => url('/posts'),
					 ]
				 ]);
	}
}
