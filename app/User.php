<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
	];
	
    protected $casts = [
        'email_verified_at' => 'datetime',
	];
	
	// 投稿データを戻す
	public function posts(){
		return $this->hasMany(Post::class);
	}
	public function likedPosts(){
		return $this->belongsToMany(Post::class,'likes','user_id','post_id');
	}
	// imageデータ定義
	public function images(){
		return $this->hasMany(UserImage::class);
	}
	// 画像ファイル取得
	public function coverImage(){
		return $this->hasOne(UserImage::class)
			->orderByDesc('id')
			->where('location','cover')
			->withDefault(function($userImage){
				$userImage->path = 'user-images/default_cover.jpg';
			});
	}
	public function profileImage(){
		return $this->hasOne(UserImage::class)
			->orderByDesc('id')
			->where('location','profile')
			->withDefault(function($userImage){
				$userImage->path = 'user-images/default_profile.jpg';
			});
	}
	// TODO フレンド情報を返す？？
	public function friends(){
		return $this->belongsToMany(User::class,'friends','friend_id','user_id');
	}
	// public function AauthAcessToken(){
	// 	// return $this->hasMany('\App\OauthAccessToken');
	// 	return $this->hasMany(\APP\OauthAccessToken::class);
	// }
}
