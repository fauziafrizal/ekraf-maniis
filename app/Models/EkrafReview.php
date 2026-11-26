<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkrafReview extends Model
{
    protected $table = 'ekraf_reviews';

    protected $fillable = ['creative_listing_id', 'display_name', 'rating', 'body', 'published'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }
}
