@props(['cards'])
    @foreach ($cards as [$key, $title, $description, $route, $path, $tone])
        <a class="em-experience-card em-experience-card--{{ $tone }}" href="{{ route($route) }}" data-reveal>
            <span class="em-experience-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="{{ $path }}" />
                </svg>
            </span>
            <span class="em-experience-number"
                aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <span class="em-experience-copy">
                <strong>{{ $title }}</strong>
                <span>{{ $description }}</span>
            </span>
            <span class="em-experience-action">Jelajahi <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" aria-hidden="true">
                    <path d="M5 12h14m-6-6 6 6-6 6" />
                </svg></span>
        </a>
    @endforeach
