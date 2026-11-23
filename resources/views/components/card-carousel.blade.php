@props(['id', 'label', 'count'])

<div {{ $attributes->class(['card-carousel']) }} data-card-carousel @if ($count === 1) data-single-card @endif>
    <div class="card-carousel-tools" data-card-controls hidden>
        <div class="card-carousel-guide">
            <span>Seret atau geser</span>
            <div class="card-carousel-position" data-card-position role="meter"
                aria-label="Posisi gulir {{ $label }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
        </div>
        <div class="card-carousel-buttons">
            <button type="button" data-card-prev aria-controls="{{ $id }}" aria-label="Kartu sebelumnya">Sebelumnya</button>
            <button type="button" data-card-next aria-controls="{{ $id }}" aria-label="Kartu berikutnya">Berikutnya</button>
        </div>
    </div>
    <div id="{{ $id }}" class="card-carousel-track" data-card-track tabindex="0" role="group" aria-label="{{ $label }}">
        {{ $slot }}
    </div>
</div>
