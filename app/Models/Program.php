<?php

namespace App\Models;

use App\Casts\LocalDateTime;
use App\Enums\ProgramStatus;
use App\Models\Concerns\HasPublication;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasPublication;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'body',
        'audience',
        'status',
        'starts_at',
        'location',
        'registration_url',
        'published_at',
    ];

    protected $attributes = [
        'status' => ProgramStatus::Wacana->value,
    ];

    protected function casts(): array
    {
        return [
            'status' => ProgramStatus::class,
            'starts_at' => LocalDateTime::class,
            'published_at' => LocalDateTime::class,
        ];
    }

    public function registrationUrl(): ?string
    {
        $url = trim((string) $this->registration_url);

        if (! $this->status->canOpenRegistration() || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['https', 'http'], true) ? $url : null;
    }
}
