<?php

namespace App\Casts;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/** @implements CastsAttributes<CarbonImmutable, \DateTimeInterface|string|int> */
final class LocalDateTime implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?CarbonImmutable
    {
        return $value === null ? null : CarbonImmutable::parse($value, config('app.timezone'));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (blank($value)) {
            return null;
        }

        $timezone = config('app.timezone');
        $date = is_numeric($value)
            ? CarbonImmutable::createFromTimestamp($value, $timezone)
            : CarbonImmutable::parse($value, $timezone)->setTimezone($timezone);

        return $date->format($model->getDateFormat());
    }
}
