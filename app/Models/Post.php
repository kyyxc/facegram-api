<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    protected $table = 'posts';
    public $timestamps = false;
    protected $fillable = [
        'caption',
        'user_id'
    ];



    public function attachments(){
        return $this->hasMany(Attacments::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
