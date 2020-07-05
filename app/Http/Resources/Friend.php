<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Friend extends JsonResource
{
    public function toArray($request)
    {
		// return parent::toArray($request);
		return [
			'data' => [
				'type' => 'friend-request',
				'friend_request_id' => $this->id,
				'attributes' => [
					'confirmed_at' => optional($this->confirmed_at)->diffForHumans(),
					'friend_id'    => $this->friend_id,
					'user_id'      => $this->user_id,
				],
			],
			'links' => [
				'self' => url('/users/'.$this->friend_id),
			]
		];
    }
}
