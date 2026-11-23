@extends('layouts.public')

@section('content')
    <section class="editorial-hero" aria-labelledby="home-title">
        <div class="site-shell">
            <div @class(['editorial-hero-stage', 'editorial-hero-stage--photo' => $heroImage])>
                <div class="editorial-hero-text" data-reveal>
                    <p class="editorial-place">{{ $site['location'] }}</p>
                    <h1 id="home-title">{{ $site['short_name'] }}</h1>
                    <p class="editorial-slogan">{{ $site['slogan'] }}</p>
                    <p class="editorial-identity">{{ $site['name'] }} <span>{{ $site['period'] }}</span></p>
                    <div class="editorial-actions">
                        @if ($programs->isEmpty() && $galleryHighlights->isNotEmpty())
                            <a class="button-primary" href="#sorotan-kegiatan">Lihat Kegiatan</a>
                        @else
                            <a class="button-primary" href="{{ route('programs.index') }}">Lihat Program</a>
                        @endif
                        @if (count($contacts))
                            <a class="button-secondary" href="{{ route('contact') }}">Hubungi Kami</a>
                        @elseif (count($potentials))
                            <a class="button-secondary" href="#potensi">Jelajahi Potensi</a>
                        @endif
                    </div>
                </div>

                @if ($heroImage)
                    <div class="editorial-hero-media">
                        <img src="{{ $heroImage['url'] }}" alt="{{ $heroImage['alt'] }}"
                            width="{{ $heroImage['width'] }}" height="{{ $heroImage['height'] }}"
                            decoding="async" fetchpriority="high">
                    </div>
                @endif
            </div>

            @if ($heroImage)
                <p class="editorial-hero-credit">
                    <span>{{ $heroImage['location'] }}</span>
                    <span>
                        Foto oleh
                        @if (filled($heroPhotoDetails['source_url'] ?? null))
                            <a href="{{ $heroPhotoDetails['source_url'] }}">{{ $heroImage['credit'] }}</a>
                        @else
                            {{ $heroImage['credit'] }}
                        @endif
                    </span>
                    @if (filled($heroPhotoDetails['license'] ?? null))
                        <span>
                            @if (filled($heroPhotoDetails['license_url'] ?? null))
                                <a href="{{ $heroPhotoDetails['license_url'] }}">{{ $heroPhotoDetails['license'] }}</a>
                            @else
                                {{ $heroPhotoDetails['license'] }}
                            @endif
                        </span>
                    @endif
                    @if (filled($heroPhotoDetails['changes'] ?? null))
                        <span>{{ $heroPhotoDetails['changes'] }}</span>
                    @endif
                </p>
            @endif
        </div>
    </section>

    <section class="editorial-intro" aria-labelledby="intro-title">
        <div class="site-shell editorial-intro-grid" data-reveal>
            <h2 id="intro-title" class="section-title">Berkarya bersama di Maniis</h2>
            <div>
                <p class="prose-copy">{{ $site['intro'] }}</p>
                <a class="editorial-link" href="{{ route('profile') }}#pengurus">Kenali pengurus</a>
            </div>
        </div>
    </section>

    @if ($galleryHighlights->isNotEmpty())
        <section id="sorotan-kegiatan" class="editorial-section home-gallery" aria-labelledby="gallery-highlights-title">
            <div class="site-shell">
                <div class="editorial-section-heading" data-reveal>
                    <h2 id="gallery-highlights-title" class="section-title">Sorotan Kegiatan</h2>
                    <a class="editorial-link" href="{{ route('activities.index') }}#galeri">Lihat galeri kegiatan</a>
                </div>
                <div class="home-gallery-grid">
                    @foreach ($galleryHighlights as $highlight)
                        <x-public-photo
                            :photo="$highlight['image']"
                            :caption="$highlight['caption']"
                            :preview="true"
                            credit-prefix="Sumber foto ·"
                            class="activity-gallery-photo home-gallery-card"
                            image-class="activity-gallery-image"
                            data-reveal
                        />
                    @endforeach
                </div>
            </div>
        </section>

        <x-gallery-preview-dialog />
    @endif

    @if (count($potentials))
        <section id="potensi" class="editorial-section editorial-section--potential" aria-labelledby="potential-title">
            <div class="site-shell">
                <div class="editorial-section-heading" data-reveal>
                    <h2 id="potential-title" class="section-title">Potensi Maniis</h2>
                    <a class="editorial-link" href="{{ route('maniis') }}">Jelajahi Potensi Maniis</a>
                </div>
                <x-card-carousel id="potensi-cards" label="Kartu potensi Maniis" :count="count($potentials)">
                    @foreach ($potentials as $potential)
                        <article class="potential-card" data-reveal>
                            @if ($potential['photo'])
                                <x-public-photo :photo="$potential['photo']" image-class="aspect-[16/9] w-full object-cover" />
                            @endif
                            <div class="potential-card-text">
                                <h3><a href="{{ route('maniis') }}#{{ Str::slug($potential['title']) }}">{{ $potential['title'] }}</a></h3>
                                <p>{{ $potential['summary'] }}</p>
                                <span class="potential-card-arrow" aria-hidden="true"></span>
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
                <div class="editorial-section-heading" data-reveal>
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
                <div class="editorial-section-heading" data-reveal>
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
        <div class="site-shell editorial-contact-grid" data-reveal>
            <h2 id="contact-title" class="section-title">Kolaborasi dengan {{ $site['short_name'] }}</h2>
            <div>
                <p>Warga, pelaku usaha, sekolah, komunitas, dan calon mitra dapat menyampaikan ide kerja sama melalui kanal resmi pengurus.</p>
                <a class="editorial-link" href="{{ route('contact') }}">{{ count($contacts) ? 'Lihat kanal resmi' : 'Informasi kontak' }}</a>
            </div>
        </div>
    </section>
@endsection
