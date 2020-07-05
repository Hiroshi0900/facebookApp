<?php

namespace Tests\Feature;
use App\Post;
use App\User;
use App\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostCommentsTest extends TestCase
{
	use RefreshDatabase;
	
    public function test_user_can_comment_post(){
		$this->withoutExceptionHandling();
		$this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 123]);

        $response = $this->post('/api/posts/'.$post->id.'/comment', [
            'body' => 'A great comment here.',
        ])
            ->assertStatus(200);

        $comment = Comment::first();
        $this->assertCount(1, Comment::all());
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
        $this->assertEquals('A great comment here.', $comment->body);
        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'comments',
                        'comment_id' => 1,
                        'attributes' => [
                            'commented_by' => [
                                'data' => [
                                    'user_id' => $user->id,
                                    'attributes' => [
                                        'name' => $user->name,
                                    ]
                                ]
                            ],
                            'body' => 'A great comment here.',
                            'commented_at' => $comment->created_at->diffForHumans(),
                        ]
                    ],
                    'links' => [
                        'self' => url('/posts/123'),
                    ]
                ]
            ],
            'links' => [
                'self' => url('/posts'),
            ]
        ]);
		// $this->withoutExceptionHandling();
		// $this->actingAs($user = factory(User::class)->create() , 'api');
		// $post = factory(Post::class)->create(['id' => 123]);
		// $response = $this->post('/api/posts/'.$post->id.'/comment',[
		// 	'body' => 'required',
		// ])->assertStatus(200);
		// $comment = Comment::first();
		// $this->assertCount(1,Comment::all());
		// $this->assertEquals($user->id,$comment->user_id);
		// $this->assertEquals($post->id,$comment->post_id);
		// $this->assertEquals('A great comment here',$comment->body);

		// $response->assertJson([
		// 	'data' => [
		// 		[
		// 			'data' => [
		// 				'types'      => 'comments',
		// 				'like_id'    => 1,
		// 				'attributes' => [
		// 					'commented_by' => [
		// 						'data' => [
		// 							'user_id' => $user->id,
		// 							'attributes' =>[
		// 								'name'    => $user->name,
		// 							],
		// 						],
		// 					],
		// 					'body' => 'A great comment here',
		// 					'commented_at' => $comment->created_at->diffForHumans(),
		// 				],
		// 			],
		// 			'links' => [
		// 				'self' => url('/posts/123'),
		// 			]
		// 		],
		// 	],
		// 	'links' => [
		// 		'self' => url('/posts'),
		// 	],
		// ]);
	}
	// TODO コメントがつくと一番上に行く？ってこと？
	public function test_body_is_required_to_leave_a_comment_on_post(){
		// $this->withoutExceptionHandling();
		$this->actingAs($user = factory(User::class)->create() , 'api');
		$post = factory(Post::class)->create(['id' => 123]);
		$response = $this->post('/api/posts/'.$post->id.'/comment',[
			'body' => '',
		])->assertStatus(422);
        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('body', $responseString['errors']['meta']);
	}
	// TODO 投稿内容にコメントがつく
	public function test_post_are_returned_with_comment(){
		$this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 123, 'user_id' => $user->id]);
        $this->post('/api/posts/'.$post->id.'/comment', [
            'body' => 'A great comment here.',
        ]);
        $response = $this->get('/api/posts');

        $comment = Comment::first();
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'attributes' => [
                                'comments' => [
                                    'data' => [
                                        [
                                            'data' => [
                                                'type' => 'comments',
                                                'comment_id' => 1,
                                                'attributes' => [
                                                    'commented_by' => [
                                                        'data' => [
                                                            'user_id' => $user->id,
                                                            'attributes' => [
                                                                'name' => $user->name,
                                                            ]
                                                        ]
                                                    ],
                                                    'body' => 'A great comment here.',
                                                    'commented_at' => $comment->created_at->diffForHumans(),
                                                ]
                                            ],
                                            'links' => [
                                                'self' => url('/posts/123'),
                                            ]
                                        ]
                                    ],
                                    'comment_count' => 1,
                                ],
                            ]
                        ]
                    ]
                ]
            ]);
	}
}
