<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->group(function(){
	
	// Route::get('/user',function(Request $request){
	// 	return $request->user();
	// });
	// TODO APIではないの？？
	Route::get('auth-user','AuthUserController@show');
	Route::post('logout','UserController@logoutApi');
	
	Route::apiResources([
		'/posts'                    => 'PostController',
		'/posts/{post}/like'        => 'PostLikeController',
		'/posts/{post}/comment'     => 'PostCommentController',
		'/users'                    => 'UserController',
		'/users/{user}/posts'       => 'UserPostController',
		'/friend-request'           => 'FriendRequestController',
		'/friend-request-response'  => 'FriendRequestResponseController',
		'/user-images'              => 'UserImageController',
	]);
});

