<?php

namespace Tests\Feature;

use App\User;
use App\UserImage;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserImagesTest extends TestCase
{
	use RefreshDatabase;
	// setupは全てのテストで通る
	protected function setUp(): void{
		parent::setUp();
		// TODO Storage??
		Storage::fake('public');
	}
	// 画像をアップロードできるかテスト
	public function test_image_can_be_uploaded(){
		$this->withoutExceptionHandling();
		$this->actingAs($user = factory(User::class)->create(),'api');

		$file = HttpUploadedFile::fake()->image('user-image.jpg');
		
		$response = $this->post('/api/user-images',[
			'image'    => $file,
			'width'    => 850,
			'height'   => 300,
			'location' => 'cover',
		])->assertStatus(201);

		Storage::disk('public')->assertExists('/user-images/'.$file->hashName());
		$userImage = UserImage::first();
		// データがあっているかチェック
		$this->assertEquals('storage/user-images/'.$file->hashName(),'storage/'.$userImage->path);
		$this->assertEquals("850",$userImage->width);
		$this->assertEquals("300",$userImage->height);
		$this->assertEquals("cover",$userImage->location);
		$this->assertEquals($user->id,$userImage->user_id);

		$response->assertJson([
			'data' => [
				'type' => 'user-images',
				'user_image_id' => $userImage->id,
				'attributes'   => [
					'path'     => url('storage/'.$userImage->path),
					'width'    => $userImage->width,
					'height'   => $userImage->height,
					'location' => $userImage->location,
				]
			],
			'links' => [
				'self' => url('/users/'.$user->id),
			],
		]);
	}
	public function test_user_are_returned_with_their_image(){
		$this->withoutExceptionHandling();
		$this->actingAs($user = factory(User::class)->create(),'api');
		$file = HttpUploadedFile::fake()->image('user-image.jpg');
		$this->post('/api/user-images',[
			'image'    => $file,
			'width'    => 850,
			'height'   => 300,
			'location' => 'cover',
		])->assertStatus(201);
		$this->post('/api/user-images',[
			'image'    => $file,
			'width'    => 850,
			'height'   => 300,
			'location' => 'profile',
		])->assertStatus(201);

		$response = $this->get('/api/users/'.$user->id);
		// $userImage = UserImage::first();
		$response->assertJson([
			'data' => [
				'type'       => 'users',
				'user_id'    => $user->id,
				'attributes' => [
					'cover_image'    => [
						'data' => [
							'type' => 'user-images',
							'user_image_id' => 1,
							'attributes' => [],
						],
					],
					'profile_image'  => [
						'data' => [
							'type' => 'user-images',
							'user_image_id' => 2,
							'attributes' => [],
						],
					],
				]
			],
			'links' => [
				'self' => url('/users/'.$user->id),
			]
		]);
	}
}
