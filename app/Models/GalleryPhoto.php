<?php

namespace App\Models;

use App\Casts\LocalDateTime;
use App\Models\Concerns\HasDisplayOrder;
use App\Models\Concerns\HasPublication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryPhoto extends Model
{
    use HasDisplayOrder, HasPublication;

    protected $fillable = [
        'activity_id',
        'image_path',
        'alt_text',
        'caption',
        'credit',
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

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->published()->where(function (Builder $query): void {
            $query->whereNull('activity_id')
                ->orWhereHas('activity', fn (Builder $activity): Builder => $activity->published());
        });
    }
}
