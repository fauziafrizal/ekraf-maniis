<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkrafPoll extends Model
{
    protected $table = 'ekraf_polls';

    protected $fillable = ['title', 'options', 'open'];

    protected function casts(): array
    {
        return ['options' => 'array', 'open' => 'boolean'];
    }
}
