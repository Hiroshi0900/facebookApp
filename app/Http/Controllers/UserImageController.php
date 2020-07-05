<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserImage as UserImageResource;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class UserImageController extends Controller
{
    public function store(){
		$data = request()->validate([
			'image'    => '',
			'width'    => '',
			'height'   => '',
			'location' => '',
		]);

		$image = $data['image']->store('user-images','public');
		
		// TODO Image追加
		Image::make($data['image'])
			->fit($data['width'],$data['height'])
			->save(storage_path('app/public/user-images/'.$data['image']->hashName()));

		$userImage = auth()->user()->images()->create([
			'path'     => $image,
			'width'    => $data['width'],
			'height'   => $data['height'],
			'location' => $data['location'],
		]);
		// return response([],201);
		return new UserImageResource($userImage);
	}
}
