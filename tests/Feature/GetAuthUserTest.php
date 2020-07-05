<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetAuthUserTest extends TestCase
{
	use RefreshDatabase;
	// ログインユーザーを取得できるかテスト
	public function test_auth_user_can_fetch(){
		$this->withoutExceptionHandling();
		$this->actingAs($user = factory(\App\User::class)->create(),'api');

		$response = $this->get('/api/auth-user');

		$response->assertStatus(200)
		        ->assertJson([
					'data' => [
						'user_id' => $user->id,
						'attributes' => [
							'name'    => $user->name,
						]
					],
					'links' => [
						'self' => url('/users/'.$user->id),
					]
				]);
	}
}
