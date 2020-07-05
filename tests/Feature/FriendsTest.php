<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FriendTest extends TestCase
{
	use RefreshDatabase;

	public function test_user_can_send_friend_request(){
		// 例外処理を通す
		$this->withoutExceptionHandling();
		// ユーザー情報の取得
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		// もう一人のユーザー　おそらく送信する相手側？？
		$anotherUser = factory(\App\User::class)->create();

		// フレンド申請データを取得する
		$response = $this->post('/api/friend-request',[
			'friend_id' => $anotherUser->id,
		])->assertStatus(200);

		$friendRequest = \App\Friend::first();
		$this->assertNotNull($friendRequest);

		$this->assertEquals($anotherUser->id,$friendRequest->friend_id);
		$this->assertEquals($user->id,$friendRequest->user_id);

		// データのJsonアサート
		$response->assertJson([
			'data' => [
				'type' => 'friend-request',
				'friend_request_id' => $friendRequest->id,
				'attributes' => [
					'confirmed_at' => null,
				],
			],
			'links' => [
				'self' => url('/users/'.$anotherUser->id),
			]
		]);
	}
	
	public function test_user_can_send_friend_request_only_once(){
		// 例外処理を通す
		$this->withoutExceptionHandling();
		// ユーザー情報の取得
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		// もう一人のユーザー　おそらく送信する相手側？？
		$anotherUser = factory(\App\User::class)->create();

		// フレンド申請データを取得する
		$this->post('/api/friend-request',[
			'friend_id' => $anotherUser->id,
		])->assertStatus(200);
		$this->post('/api/friend-request',[
			'friend_id' => $anotherUser->id,
		])->assertStatus(200);

		$friendRequests = \App\Friend::all();
		$this->assertCount(1,$friendRequests);
	}

	public function test_user_only_valid_can_be_friend_request(){
		// 例外処理を通す
		// $this->withoutExceptionHandling();
		// ユーザー情報の取得
		$this->actingAs($user = factory(\App\User::class)->create(),'api');

		// フレンド申請データを取得する
		$response = $this->post('/api/friend-request',[
			'friend_id' => 123,
		])->assertStatus(404);

		$this->assertNull(\App\Friend::first());
		$response->assertJson([
			'errors' => [
				'code'   => 404,
				'title'  => 'user not found',
				'detail' => '取得できてないよ',
			]
		]);
	}

	// フレンドリクエストの承認
	public function test_friend_can_be_accepted(){
		// 例外処理を通す
		$this->withoutExceptionHandling();
		// ユーザー情報の取得
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		// もう一人のユーザー　おそらく送信する相手側？？
		$anotherUser = factory(\App\User::class)->create();

		// フレンド申請データを取得する
		$response = $this->post('/api/friend-request',[
			'friend_id' => $anotherUser->id,
		])->assertStatus(200);

		// フレンドリクエストを投げる
		$response = $this->actingAs($anotherUser,'api')
		    ->post('/api/friend-request-response',[
				'user_id' => $user->id,
				'status'   => 1,
			])->assertStatus(200);
		$friendRequest = \App\Friend::first();
		$this->assertNotNull($friendRequest->confirmed_at);
		$this->assertInstanceOf(Carbon::class, $friendRequest->confirmed_at);
		// TODO startOfSecondって何
		$this->assertEquals(now()->startOfSecond(), $friendRequest->confirmed_at);
		$this->assertEquals(1, $friendRequest->status);
		// jsonのアサート
		$response->assertJson([
			'data' => [
				'type' => 'friend-request',
				'friend_request_id' => $friendRequest->id,
				'attributes' => [
					'confirmed_at' => $friendRequest->confirmed_at->diffForHumans(),
					// フレンドIDも返す
					'friend_id'    => $friendRequest->friend_id,
					'user_id'      => $friendRequest->user_id,
				],
			],
			'links' => [
				'self' => url('/users/'.$anotherUser->id),
			]
		]);
	}

	// 有効なフレンドリクエストのみ受け入れることができるテスト
	public function test_only_valid_friend_request_can_be_accepted(){
		$anotherUser = factory(\App\User::class)->create();
		// フレンドリクエストを投げる
		$response = $this->actingAs($anotherUser,'api')
		    ->post('/api/friend-request-response',[
		    	'user_id' => 123,
		    	'status'   => 1,
			])->assertStatus(404);
		$this->assertNull(\App\Friend::first());
		$response->assertJson([
			'errors' => [
				'code'   => 404,
				'title'  => 'friend request not found',
				'detail' => '取得できてないよ',
			]
		]);
	}
	public function test_only_recipient_can_be_accepted(){
		// ユーザー情報の取得
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		// もう一人のユーザー　おそらく送信する相手側？？
		$anotherUser = factory(\App\User::class)->create();

		// フレンド申請データを取得する
		$this->post('/api/friend-request',[
			'friend_id' => $anotherUser->id,
		])->assertStatus(200);
		$response = $this->actingAs(factory(\App\User::class)->create(),'api')
		    ->post('/api/friend-request-response',[
		    	'user_id' => $user->id,
		    	'status'   => 1,
			])->assertStatus(404);
		$friendRequest = \App\Friend::first();
		$this->assertNull($friendRequest->confirmed_at);
		$response->assertJson([
			'errors' => [
				'code'   => 404,
				'title'  => 'friend request not found',
				'detail' => '取得できてないよ',
			]
		]);
	}
	
	// フレンドリクエストにユーザーIDが必要テスト
	public function test_friend_id_is_required(){
		$response = $this->actingAs($user = factory(\App\User::class)->create(),'api')
		        ->post('/api/friend-request',[
					'friend_id' => '',
				]);
		// TODO デコードした値を取得する
		$responseString = json_decode($response->getContent() , true);
		$this->assertArrayHasKey('friend_id',$responseString['errors']['meta']);
        // dd($responseString);
	}

	// フレンドリクエストにはユーザーIDとステータスが必要テスト
	public function test_user_id_and_status_is_required(){
		// ユーザーデータ取得
		$response = $this->actingAs($user = factory(\App\User::class)->create(),'api')
		    ->post('/api/friend-request-response',[
				'user_id' => '',
				'status'  => '',
			])->assertStatus(422);
		$responseString = json_decode($response->getContent(),true);
		$this->assertArrayHasKey('user_id',$responseString['errors']['meta']);
		$this->assertArrayHasKey('status' ,$responseString['errors']['meta']);
	}

	// フェッチした時に友情？を取得するテスト(自分からフレンド)
	public function test_friendship_retrieved_when_fetch_profile(){
		// ユーザーを二件取得する
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		$anotherUser = factory(\App\User::class)->create();

		// フレンドリクエストデータ取得
		$friendRequest = \App\Friend::create([
			'user_id'      => $user->id,
			'friend_id'    => $anotherUser->id,
			'confirmed_at' => now()->subDay(), //now関数のsubDayは1日前を取得する
			'status'       => 1,
		]);
		// ユーザーデータを取得する
		$this->get('/api/users/'.$anotherUser->id)
		    ->assertStatus(200)
		    ->assertJson([
				'data' => [
					'attributes' => [
						'friendship' => [
							'data' => [
								'friend_request_id' => $friendRequest->id,
								'attributes' => [
									'confirmed_at' => '1 day ago',
								]
							]
						],
					],
				],
			]);
	}
	
	// フェッチした時に友情？を取得するテスト(フレンドから自分)
	public function test_inverse_friendship_retrieved_when_fetch_profile(){
		// ユーザーを二件取得する
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		$anotherUser = factory(\App\User::class)->create();

		// フレンドリクエストデータ取得
		$friendRequest = \App\Friend::create([
			'friend_id'      => $user->id,
			'user_id'        => $anotherUser->id,
			'confirmed_at'   => now()->subDay(), //now関数のsubDayは1日前を取得する
			'status'         => 1,
		]);
		// ユーザーデータを取得する
		$this->get('/api/users/'.$anotherUser->id)
		    ->assertStatus(200)
		    ->assertJson([
				'data' => [
					'attributes' => [
						'friendship' => [
							'data' => [
								'friend_request_id' => $friendRequest->id,
								'attributes' => [
									'confirmed_at' => '1 day ago',
								]
							]
						],
					],
				],
			]);
	}
	// フレンドリクエストの無視
	public function test_only_recipient_can_be_ignored(){
		// 例外処理を通す
		$this->withoutExceptionHandling();
		// ユーザー情報の取得
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		// もう一人のユーザー　おそらく送信する相手側？？
		$anotherUser = factory(\App\User::class)->create();

		// フレンド申請データを取得する
		$response = $this->post('/api/friend-request',[
			'friend_id' => $anotherUser->id,
		])->assertStatus(200);

		// フレンドリクエストを削除する
		$response = $this->actingAs($anotherUser,'api')
		    ->delete('/api/friend-request-response/delete',[
				'user_id' => $user->id,
			])->assertStatus(204);
		$friendRequest = \App\Friend::first();
		$this->assertNull($friendRequest);
		$response->assertNoContent();
	}

	// ユーザーからのフレンドリクエストの無視？？
	public function test_only_recipient_can_ignore_friend_request(){
		$this->actingAs($user = factory(\App\User::class)->create(),'api');
		$anotherUser = factory(\App\User::class)->create();
		// フレンド申請データを取得する
		$this->post('/api/friend-request',[
			'friend_id' => $anotherUser->id,
		])->assertStatus(200);

		$response = $this->actingAs(factory(\App\User::class)->create(),'api')
		    ->delete('/api/friend-request-response/delete',[
		    	'user_id' => $user->id,
			])->assertStatus(404);
		
		$friendRequest = \App\Friend::first();
		$this->assertNull($friendRequest->confirmed_at);
		$this->assertNull($friendRequest->status);
		$response->assertJson([
			'errors' => [
				'code'   => 404,
				'title'  => 'friend request not found',
				'detail' => '取得できてないよ',
			]
		]);
	}

	// TODO ?? フレンドリクエストの削除？？
	public function test_user_id_and_status_is_ignore(){
		// ユーザーデータ取得
		$response = $this->actingAs($user = factory(\App\User::class)->create(),'api')
		    ->delete('/api/friend-request-response/delete',[
				'user_id' => '',
			])->assertStatus(422);
		$responseString = json_decode($response->getContent(),true);
		$this->assertArrayHasKey('user_id',$responseString['errors']['meta']);
	}
}
