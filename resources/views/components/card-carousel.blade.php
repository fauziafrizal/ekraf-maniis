@props(['id', 'label', 'count', 'trackClass' => '', 'premium' => false])

<div {{ $attributes->class(['card-carousel', 'em-drag-rail' => $premium]) }} data-card-carousel
    @if ($count === 1) data-single-card @endif>
    <div class="card-carousel-tools" data-card-controls @if(!$premium) hidden @endif>
        <div class="card-carousel-guide">
            <span class="em-drag-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 12h18M7 8l-4 4 4 4m10-8 4 4-4 4"/></svg> Klik-tahan atau geser</span>
            <div class="card-carousel-position" data-card-position role="meter"
                aria-label="Posisi gulir {{ $label }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
        </div>
        <div class="card-carousel-buttons">
            <button type="button" data-card-prev aria-controls="{{ $id }}"
                aria-label="Kartu sebelumnya">Sebelumnya</button>
            <button type="button" data-card-next aria-controls="{{ $id }}"
                aria-label="Kartu berikutnya">Berikutnya</button>
        </div>
    </div>
    <div id="{{ $id }}" class="card-carousel-track {{ $trackClass }}" data-card-track tabindex="0" role="group"
        aria-label="{{ $label }}">
        {{ $slot }}
    </div>
</div>
