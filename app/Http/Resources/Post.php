<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Comment as CommentResource;

class Post extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		// return parent::toArray($request);
		
		// return [
		// 	'data' => [
		// 		'type' => 'posts',
		// 		'post_id' => $this->id,
		// 		'attributes' => [
		// 			'posted_by' => new UserResource($this->user),
		// 			// likes追加
		// 			'likes'     => new LikeCollection($this->likes),
		// 			// comment追加
		// 			'comments'  => new CommentCollection($this->comments),
		// 			'body'      => $this->body,
		// 			'image'     => $this->image,
		// 			'posted_at' => $this->created_at->diffForHumans(),
		// 		],
		// 	],
		// 	'links' => [
		// 		'self' => url('/posts/'.$this->id),
		// 	]
		// ];
		return [
            'data' => [
                'type' => 'posts',
                'post_id' => $this->id,
                'attributes' => [
                    'posted_by' => new UserResource($this->user),
                    'likes' => new LikeCollection($this->likes),
                    'comments' => new CommentCollection($this->comments),
                    'body' => $this->body,
					'image' => ($this->image) ? url('storage/'.$this->image) : url($this->image), // シンボリックリンク考慮
                    'posted_at' => $this->created_at->diffForHumans(),
                ]
            ],
            'links' => [
                'self' => url('/posts/'.$this->id),
            ]
        ];
    }
}
