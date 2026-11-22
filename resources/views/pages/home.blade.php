@extends('layouts.public')

@section('content')
    <section class="editorial-hero" aria-labelledby="home-title">
        <div class="site-shell editorial-hero-grid">
            <div class="editorial-hero-text">
                <p class="editorial-place">{{ $site['location'] }}</p>
                <h1 id="home-title">{{ $site['short_name'] }}</h1>
                <p class="editorial-slogan">{{ $site['slogan'] }}</p>
                <p class="editorial-identity">{{ $site['name'] }} <span>{{ $site['period'] }}</span></p>
                <div class="editorial-actions">
                    <a class="button-primary" href="{{ route('programs.index') }}">Lihat Program</a>
                    @if (count($contacts))
                        <a class="button-secondary" href="{{ route('contact') }}">Hubungi Kami</a>
                    @endif
                </div>
            </div>

            @if ($heroImage)
                <x-public-photo :photo="$heroImage" :priority="true" class="editorial-hero-photo" image-class="w-full object-cover" />
            @else
                <figure class="reservoir-figure">
                    <div class="reservoir-print" aria-hidden="true">
                        <div class="reservoir-shore reservoir-shore--far"></div>
                        <div class="reservoir-shore reservoir-shore--near"></div>
                        <div class="reservoir-cages reservoir-cages--upper"></div>
                        <div class="reservoir-cages reservoir-cages--lower"></div>
                        <div class="reservoir-wake"></div>
                    </div>
                    <figcaption>Ilustrasi Waduk Cirata dan keramba jaring apung</figcaption>
                </figure>
            @endif
        </div>
    </section>

    <section class="editorial-intro" aria-labelledby="intro-title">
        <div class="site-shell editorial-intro-grid">
            <h2 id="intro-title" class="section-title">Berkarya bersama di Maniis</h2>
            <div>
                <p class="prose-copy">{{ $site['intro'] }}</p>
                <a class="editorial-link" href="{{ route('profile') }}#pengurus">Kenali pengurus</a>
            </div>
        </div>
    </section>

    @if (count($potentials))
        <section id="potensi" class="editorial-section editorial-section--potential" aria-labelledby="potential-title">
            <div class="site-shell">
                <div class="editorial-section-heading">
                    <h2 id="potential-title" class="section-title">Potensi Maniis</h2>
                    <a class="editorial-link" href="{{ route('maniis') }}">Jelajahi Potensi Maniis</a>
                </div>
                <x-card-carousel id="potensi-cards" label="Kartu potensi Maniis" :count="count($potentials)">
                    @foreach ($potentials as $potential)
                        <article class="potential-card">
                            @if ($potential['photo'])
                                <x-public-photo :photo="$potential['photo']" image-class="aspect-[16/9] w-full object-cover" />
                            @endif
                            <div class="potential-card-text">
                                <h3><a href="{{ route('maniis') }}#{{ Str::slug($potential['title']) }}">{{ $potential['title'] }}</a></h3>
                                <p>{{ $potential['summary'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </x-card-carousel>
            </div>
        </section>
    @endif

    @if ($programs->isNotEmpty())
        <section class="editorial-section" aria-labelledby="programs-title">
            <div class="site-shell">
                <div class="editorial-section-heading">
                    <h2 id="programs-title" class="section-title">Program</h2>
                    <a class="editorial-link" href="{{ route('programs.index') }}">Lihat semua program</a>
                </div>
                <x-card-carousel id="program-cards" label="Kartu program" :count="$programs->count()">
                    @foreach ($programs as $program)
                        <div class="home-preview-card">
                            <x-program-card :program="$program" heading="h3" />
                        </div>
                    @endforeach
                </x-card-carousel>
            </div>
        </section>
    @endif

    @if ($activities->isNotEmpty())
        <section class="editorial-section" aria-labelledby="activities-title">
            <div class="site-shell">
                <div class="editorial-section-heading">
                    <h2 id="activities-title" class="section-title">Dokumentasi kegiatan</h2>
                    <a class="editorial-link" href="{{ route('activities.index') }}">Lihat semua kegiatan</a>
                </div>
                <x-card-carousel id="kegiatan-cards" label="Kartu kegiatan" :count="$activities->count()">
                    @foreach ($activities as $activity)
                        <div class="home-preview-card">
                            <x-activity-card :activity="$activity" :cover="$activityCovers[$activity->id] ?? null" heading="h3" />
                        </div>
                    @endforeach
                </x-card-carousel>
            </div>
        </section>
    @endif

    <section class="editorial-contact" aria-labelledby="contact-title">
        <div class="site-shell editorial-contact-grid">
            <h2 id="contact-title" class="section-title">Kolaborasi dengan {{ $site['short_name'] }}</h2>
            <div>
                <p>Warga, pelaku usaha, sekolah, komunitas, dan calon mitra dapat menyampaikan ide kerja sama melalui kanal resmi pengurus.</p>
                <a class="editorial-link" href="{{ route('contact') }}">{{ count($contacts) ? 'Lihat kanal resmi' : 'Informasi kontak' }}</a>
            </div>
        </div>
    </section>
@endsection
