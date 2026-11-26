<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkrafFeedback extends Model
{
    protected $table = 'ekraf_feedback';

    protected $fillable = ['rating', 'body'];

    protected function casts(): array
    {
        return [];
    }
}
