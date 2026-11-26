<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkrafCredential extends Model
{
    protected $table = 'ekraf_credentials';

    protected $fillable = ['token', 'kind', 'number', 'recipient', 'title', 'issued_on', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
