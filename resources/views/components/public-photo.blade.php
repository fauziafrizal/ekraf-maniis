@props([
    'photo',
    'priority' => false,
    'caption' => null,
    'imageClass' => 'aspect-[4/3] w-full object-cover',
    'preview' => false,
    'creditPrefix' => 'Foto oleh',
])

<figure {{ $attributes }}>
    @if ($preview)
        <a class="activity-gallery-link" href="{{ $photo['url'] }}" data-gallery-preview
            aria-label="Perbesar foto. {{ $photo['alt'] }}">
    @endif
    <img draggable="false" src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}"
        class="{{ $imageClass }}" decoding="async"
        @if ($priority) fetchpriority="high" @else loading="lazy" @endif>
    @if ($preview)
        <span class="activity-gallery-action" aria-hidden="true">Lihat foto</span>
        </a>
    @endif
    @if ($caption || filled($photo['location'] ?? null) || filled($photo['credit'] ?? null))
        <figcaption class="space-y-1 px-4 py-3 text-sm leading-relaxed wrap-anywhere text-[#416656]">
            @if ($caption)
                <span class="block">{{ $caption }}</span>
            @endif
            @if (filled($photo['location'] ?? null) || filled($photo['credit'] ?? null))
                <span class="block">
                    {{ collect([$photo['location'] ?? null, filled($photo['credit'] ?? null) ? $creditPrefix . ' ' . $photo['credit'] : null])->filter()->join(' · ') }}
                </span>
            @endif
        </figcaption>
    @endif
</figure>
