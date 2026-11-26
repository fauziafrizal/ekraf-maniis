<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkrafDocument extends Model
{
    protected $table = 'ekraf_documents';

    protected $fillable = ['kind', 'number', 'title', 'document_date', 'body', 'file_path'];

    protected function casts(): array
    {
        return [];
    }
}
