@extends('layouts.public')
@section('content')
    <x-page-heading title="Kalender Kreatif" eyebrow="Agenda Ekraf Maniis"
        description="Jelajahi jadwal program yang telah dipublikasikan. Simpan pengingat kalender pada perangkat Anda." />
    <div class="site-shell em-service-layout">
        <section class="em-tool-card" aria-labelledby="calendar-title">
            <div class="em-calendar-head">
                @unless(config('ekraf.static_preview'))<a class="button-secondary" href="{{ route('creative.calendar', ['bulan' => $previous]) }}">Bulan lalu</a>@endunless
                <h2 id="calendar-title" class="section-title">{{ $month->translatedFormat('F Y') }}</h2>
                @unless(config('ekraf.static_preview'))<a class="button-secondary" href="{{ route('creative.calendar', ['bulan' => $next]) }}">Bulan depan</a>@endunless
            </div>
            <div class="em-calendar-grid">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                    <span class="em-calendar-weekday">{{ $day }}</span>
                @endforeach
                @for ($i = 1; $i < $month->dayOfWeekIso; $i++)
                    <span aria-hidden="true"></span>
                @endfor
                @for ($day = 1; $day <= $month->daysInMonth; $day++)
                    @php($events = $byDay->get($day, collect()))
                    <div @class([
                        'em-calendar-day',
                        'em-calendar-has-event' => $events->isNotEmpty(),
                        'em-calendar-today' => $month->copy()->day($day)->isToday(),
                    ])>
                        <span>{{ $day }}</span>
                        @if ($events->isNotEmpty())
                            <a href="#tanggal-{{ $day }}"
                                aria-label="{{ $events->count() }} agenda pada tanggal {{ $day }}">{{ $events->count() }}
                                agenda</a>
                        @endif
                    </div>
                @endfor
            </div>
            <p class="em-service-note">Waktu menggunakan WIB. Jadwal dapat berubah sesuai publikasi pengurus. @if(config('ekraf.static_preview')) Kalender ini merupakan snapshot saat publikasi situs. @endif</p>
        </section>
        <section aria-labelledby="calendar-list">
            <h2 id="calendar-list" class="section-title">Agenda bulan ini</h2>
            @forelse($byDay as $day=>$events)
                <div id="tanggal-{{ $day }}" class="em-tool-card">
                    <h3>{{ $month->copy()->day($day)->translatedFormat('j F Y') }}</h3>
                    @foreach ($events as $program)
                        <article class="em-calendar-event">
                            <x-program-status :status="$program->status" />
                            <h4><a href="{{ route('programs.show', $program->slug) }}">{{ $program->title }}</a></h4>
                            <p>{{ $program->starts_at->format('H.i') . ' WIB' }} @if ($program->location)
                                    · {{ $program->location }}
                                @endif
                            </p>
                            <a class="editorial-link" href="{{ route('creative.calendar.ics', $program->slug) }}">Simpan ke
                                kalender (.ics)</a>
                        </article>
                    @endforeach
                </div>
            @empty
                <p class="em-service-note">Belum ada agenda terpublikasi pada bulan ini.</p>
            @endforelse
        </section>
    </div>
@endsection
