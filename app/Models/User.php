<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'password',
        'bio',
        'is_private'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function following()
    {
        return $this->hasMany(Following::class, 'follower_id');
    }

    public function followers()
    {
        return $this->hasMany(Following::class, 'following_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function getFollowers(){
        return $this->belongsToMany(User::class, 'follow', 'following_id', 'foollowers_id');
    }

    public function getFollowing(){
        return $this->belongsToMany(User::class, 'follow', 'follower_id', 'following_id');
    }
}
