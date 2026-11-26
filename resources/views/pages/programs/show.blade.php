@extends('layouts.public')

@section('content')
    <header class="program-detail-hero">
        <div class="site-shell">
            <a class="detail-return" href="{{ route('programs.index') }}">Kembali ke daftar program</a>
            <div class="program-detail-masthead">
                <div class="program-detail-meta">
                    <p class="detail-kicker">Program</p>
                    <x-program-status :status="$program->status" />
                </div>
                <h1 class="detail-title">{{ $program->title }}</h1>
                <p class="detail-lead">{{ $program->summary }}</p>
            </div>
        </div>
    </header>

    <div class="site-shell program-detail-grid">
        <section class="program-detail-facts" aria-labelledby="program-information">
            <h2 id="program-information">Informasi program</h2>
            <dl>
                <div>
                    <dt>Sasaran</dt>
                    <dd>{{ $program->audience }}</dd>
                </div>
                @if ($program->starts_at)
                    <div>
                        <dt>Jadwal</dt>
                        <dd>
                            <time
                                datetime="{{ $program->starts_at->toIso8601String() }}">{{ $program->starts_at->translatedFormat('j F Y, H.i') . ' WIB' }}</time>
                        </dd>
                    </div>
                @endif
                @if (filled($program->location))
                    <div>
                        <dt>Lokasi</dt>
                        <dd>{{ $program->location }}</dd>
                    </div>
                @endif
            </dl>

            @if ($registrationUrl)
                <a class="button-primary" href="{{ $registrationUrl }}">Daftar</a>
            @endif
        </section>

        <article class="program-detail-story">
            <div data-reveal>
                <h2 class="section-title">Tentang program</h2>
                <x-content-paragraphs :text="$program->body" class="detail-body" />
            </div>

            <div class="program-detail-collaboration" data-reveal>
                <h2>Peluang kolaborasi</h2>
                <p class="prose-copy">Sampaikan ide atau pertanyaan tentang program ini kepada pengurus.</p>
                <a class="detail-return" href="{{ route('contact') }}">Kontak {{ $site['short_name'] }}</a>
            </div>
        </article>
    </div>
@endsection
