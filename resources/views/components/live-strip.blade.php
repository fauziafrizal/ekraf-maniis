<section class="em-live-strip" aria-label="Jelajahi layanan Ekraf">
    <div class="site-shell em-live-inner">
        <span class="em-live-label"><span aria-hidden="true"></span> Jelajahi</span>
        <div class="em-live-window">
            <div class="em-live-track" data-live-track>
                @for ($copy = 0; $copy < 2; $copy++)
                    <div class="em-live-group" @if ($copy) aria-hidden="true" @endif>
                        @foreach ([['Karya lokal', 'creative.market'], ['Ekraf Academy', 'creative.academy'], ['Pelajar kreatif', 'creative.students'], ['Potensi Maniis', 'maniis'], ['Agenda program', 'services'], ['Tumbuh dari Kolaborasi', 'creative.hub']] as [$label, $route])
                            <a href="{{ route($route) }}"
                                @if ($copy) tabindex="-1" @endif>{{ $label }}</a><span
                                aria-hidden="true">✦</span>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
        <button type="button" data-live-toggle disabled aria-label="Putar animasi informasi"
            aria-pressed="false">Putar</button>
    </div>
</section>
