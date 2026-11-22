<?php

namespace App\Models;

use App\Casts\LocalDateTime;
use App\Models\Concerns\HasDisplayOrder;
use App\Models\Concerns\HasPublication;
use Illuminate\Database\Eloquent\Model;

class Subsector extends Model
{
    use HasDisplayOrder, HasPublication;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'examples',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => LocalDateTime::class,
            'sort_order' => 'integer',
        ];
    }
}
