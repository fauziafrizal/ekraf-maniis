@props(['photo', 'priority' => false, 'caption' => null, 'imageClass' => 'aspect-[4/3] w-full object-cover'])

<figure {{ $attributes }}>
    <img src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}"
        class="{{ $imageClass }}" decoding="async" @if ($priority) fetchpriority="high" @else loading="lazy" @endif>
    @if ($caption || filled($photo['location'] ?? null) || filled($photo['credit'] ?? null))
        <figcaption class="space-y-1 px-4 py-3 text-sm leading-relaxed wrap-anywhere text-[#416656]">
            @if ($caption)
                <span class="block">{{ $caption }}</span>
            @endif
            @if (filled($photo['location'] ?? null) || filled($photo['credit'] ?? null))
                <span class="block">
                    {{ collect([$photo['location'] ?? null, filled($photo['credit'] ?? null) ? 'Foto oleh '.$photo['credit'] : null])->filter()->join(' · ') }}
                </span>
            @endif
        </figcaption>
    @endif
</figure>
