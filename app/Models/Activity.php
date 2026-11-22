<?php

namespace App\Models;

use App\Casts\LocalDateTime;
use App\Models\Concerns\HasPublication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasPublication;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'body',
        'event_date',
        'cover_path',
        'cover_alt',
        'cover_credit',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'immutable_date',
            'published_at' => LocalDateTime::class,
        ];
    }

    public function galleryPhotos(): HasMany
    {
        return $this->hasMany(GalleryPhoto::class);
    }
}
