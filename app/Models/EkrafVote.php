<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkrafVote extends Model
{
    protected $table = 'ekraf_votes';

    protected $fillable = ['ekraf_poll_id', 'user_id', 'choice'];

    protected function casts(): array
    {
        return [];
    }
}
