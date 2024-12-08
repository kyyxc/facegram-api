<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    protected $table = 'follow';
    public $timestamps = false;
    protected $fillable = [
        'follower_id',
        'following_id',
        'is_accepted'
    ];

    public function userFollower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function userFollowing()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
