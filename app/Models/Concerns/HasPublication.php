<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasPublication
{
    public function scopePublished(Builder $query): Builder
    {
        $column = $this->qualifyColumn('published_at');

        return $query->whereNotNull($column)->where($column, '<=', now());
    }

    public function scopeRecentlyPublished(Builder $query): Builder
    {
        return $query->published()
            ->orderByDesc($this->qualifyColumn('published_at'))
            ->orderByDesc($this->getQualifiedKeyName());
    }
}
