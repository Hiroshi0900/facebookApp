<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserCanViewProfileTest extends TestCase
{
	use RefreshDatabase;

	// ユーザー情報取得できるかテスト
	public function test_user_can_view_user_profile(){
		// 例外処理を弾く
		$this->withoutExceptionHandling();
		
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		$posts = factory(\App\Post::class)->create();

		$response = $this->get('/api/users/'.$user->id);

		$response->assertStatus(200)
		    ->assertJson([
				'data' => [
					'type' => 'users',
					'user_id' => $user->id,
					'attributes' => [
						'name' => $user->name,
					],
				],
				'links' => [
					'self' => url('/users/'.$user->id),
				]
			]);
	}
	// TODO 自分自身の投稿データを取得するテスト？？
	public function test_can_fetch_user_profile(){
		// 例外処理を弾く
		$this->withoutExceptionHandling();
		
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		$post = factory(\App\Post::class)->create(['user_id' => $user->id]);

		// TODO postsをつけるのは投稿データも取得したいから？
		$response = $this->get('/api/users/'.$user->id.'/posts');

		$response->assertStatus(200)
		    ->assertJson([
				'data' => [
					[
						'data' => [
							'type'       => 'posts',
							'post_id'    => $post->id,
							'attributes' => [
								'body'      => $post->body,
								'image'     => url('storage/'.$post->image),
								'posted_at' => $post->created_at->diffForHumans(),
								'posted_by' => [
									'data'  => [
										'attributes' => [
										    'name' => $user->name,
										]
									]
								]
							],
						],
						'links' => [
							'self' => url('/posts/'.$post->id),
						],
					]
				]
			]);
	}
}
