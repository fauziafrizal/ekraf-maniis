<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasDisplayOrder
{
    public function scopeInDisplayOrder(Builder $query): Builder
    {
        return $query->orderBy($this->qualifyColumn('sort_order'))
            ->orderBy($this->getQualifiedKeyName());
    }
}
