<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attacments extends Model
{
    /** @use HasFactory<\Database\Factories\AttacmentsFactory> */
    use HasFactory;
    protected $table = 'post_attachments';
    public $timestamps = false;
    protected $fillable = [
        'storage_path',
        'post_id'
    ];
}
