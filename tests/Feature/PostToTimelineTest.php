<?php

namespace Tests\Feature;

use App\User;
use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostToTimelineTest extends TestCase
{
	use RefreshDatabase; //テストするごとにデータベースをリフレッシュするらしい
	protected function setUp(): void{
		parent::setUp();
		Storage::fake('public');
	}
	// ポストできるかのテスト
	public function test_a_user_can_post_text(){
		// $this->withoutExceptionHandling();
		$this->actingAs($user = factory(\App\User::class)->create(),'api');

		$response = $this->post('/api/posts',[
			'body' => 'Testing Body'
		]);
		$post = Post::first();

		$this->assertCount(1,Post::all());
		$this->assertEquals($user->id,$post->user_id);
		$this->assertEquals('Testing Body',$post->body);

		$response->assertStatus(201)
		    ->assertJson([
				'data' => [
					'type' => 'posts',
					'post_id' => $post->id,
					'attributes' => [
						'posted_by' => [
							'data' => [
								'attributes' => [
									'name' => $user->name
								]
							]
						],
						'body'      => 'Testing Body'
					],
				],
				'links' => [
					'self' => url('/posts/'.$post->id),
				]
			]);
	}
	// 画像付きの投稿ができるかテスト
	public function test_a_user_can_post_text_with_image(){
		$this->withoutExceptionHandling();
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		// ファイル取得
		$file = UploadedFile::fake()->image('user-post.jpg');
		$response = $this->post('/api/posts',[
			'body'   => 'Testing Body',
			'image'  => $file,
			'width'  => 100,
			'height' => 100,
		]);
		Storage::disk('public')->assertExists('post-images/'.$file->hashName());

		$response->assertStatus(201)
		    ->assertJson([
				'data' => [
					'attributes' => [
						'body'      => 'Testing Body',
						'image' => url('storage/post-images/'.$file->hashName()),
					],
				],
			]);
	}
	
}
