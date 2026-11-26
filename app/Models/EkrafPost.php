<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkrafPost extends Model
{
    protected $table = 'ekraf_posts';

    protected $fillable = ['kind', 'title', 'display_name', 'body', 'url', 'photo_path', 'published'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }
}
