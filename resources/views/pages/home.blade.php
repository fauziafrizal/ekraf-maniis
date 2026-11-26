@extends('layouts.public')

@section('content')
    <section class="editorial-hero" aria-labelledby="home-title">
        <div class="site-shell">
            <div @class([
                'editorial-hero-stage',
                'editorial-hero-stage--photo' => $heroImage,
            ])>
                <div class="editorial-hero-text" data-reveal>
                    <div class="em-home-edition"><span aria-hidden="true"></span> Portal Ekonomi Kreatif <span
                            class="em-home-edition-separator" aria-hidden="true"></span> {{ $site['period'] }}</div>
                    <p class="editorial-place">{{ $site['location'] }}</p>
                    <h1 id="home-title">{{ $site['short_name'] }}</h1>
                    <p class="editorial-slogan">{{ $site['slogan'] }}</p>
                    <p class="editorial-identity">{{ $site['name'] }}</p>
                    <p class="em-hero-caption">Temukan potensi daerah, karya lokal, dan peluang untuk tumbuh bersama.</p>
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
                    <a class="em-home-hero-link" href="{{ route('creative.hub') }}">Jelajahi seluruh layanan digital <span
                            aria-hidden="true">↗</span></a>
                    <p class="em-hero-index">Karya lokal · Belajar · Kolaborasi</p>
                </div>

                @if ($heroImage)
                    <div class="editorial-hero-media">
                        <img draggable="false" src="{{ $heroImage['url'] }}" alt="{{ $heroImage['alt'] }}" width="{{ $heroImage['width'] }}"
                            height="{{ $heroImage['height'] }}" decoding="async" fetchpriority="high">
                        <x-hero-motion />
                        <div class="em-home-photo-caption">
                            <span>Jelajah daerah</span>
                            <strong>{{ $heroImage['location'] }}</strong>
                            <a href="{{ route('maniis') }}">Kenali potensi Maniis <span aria-hidden="true">↗</span></a>
                        </div>
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

    <x-voice-welcome />

    <x-live-strip />

    <nav class="em-discovery" aria-label="Jelajahi Ekraf Maniis">
        <div class="site-shell">
            <div class="em-home-discovery-heading">
                <div>
                    <p class="em-kicker">Akses cepat</p>
                    <h2 class="section-title">Kenali lebih dekat.</h2>
                </div>
                <p>Informasi resmi, potensi daerah, dan cerita kegiatan dalam satu tempat.</p>
            </div>
        </div>
        <div class="site-shell em-discovery-grid">
            <a class="em-discovery-link" href="{{ route('profile') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <strong>Profil organisasi</strong>
                <span>Kenali visi dan pengurus</span>
            </a>
            <a class="em-discovery-link" href="{{ route('maniis') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="m3 19 6-10 4 6 3-4 5 8H3Z" />
                    <circle cx="17" cy="5" r="2" />
                </svg>
                <strong>Potensi Maniis</strong>
                <span>Jelajahi kekayaan lokal</span>
            </a>
            <a class="em-discovery-link" href="{{ route('subsectors') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
                <strong>Subsektor kreatif</strong>
                <span>Kenali bidang ekonomi kreatif</span>
            </a>
            <a class="em-discovery-link" href="{{ route('activities.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path d="M16 3v4M8 3v4M3 10h18m-12 5 2 2 4-4" />
                </svg>
                <strong>Kegiatan</strong>
                <span>Ikuti dokumentasi dan aktivitas</span>
            </a>
        </div>
    </nav>

    <section class="em-experience-section" aria-labelledby="experience-title">
        <div class="site-shell">
            <div class="em-section-lead" data-reveal>
                <div>
                    <p class="em-kicker">Ruang tumbuh bersama</p>
                    <h2 class="section-title" id="experience-title">Satu Maniis.<br>Banyak kemungkinan.</h2>
                </div>
                <p class="prose-copy">Belajar, berkarya, dan temukan peluang baru. Pilih ruang yang sesuai dengan
                    perjalanan
                    kreatif Anda.</p>
            </div>
            <x-experience-grid compact />
            <div class="em-experience-links">
                <a class="button-primary" href="{{ route('creative.hub') }}">Jelajahi Smart Creative Hub</a>
                <a class="button-secondary" href="{{ route('services') }}">Pusat layanan dan agenda Ekraf</a>
            </div>
        </div>
    </section>

    <section class="editorial-intro" aria-labelledby="intro-title">
        <div class="site-shell editorial-intro-grid" data-reveal>
            <div class="em-home-intro-title">
                <p class="em-kicker">Tentang kami</p>
                <h2 id="intro-title" class="section-title">Berkarya bersama di Maniis</h2>
                <p class="em-home-intro-motto">{{ $site['slogan'] }}</p>
            </div>
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
                <x-card-carousel id="home-gallery-cards" label="Sorotan foto kegiatan" :count="$galleryHighlights->count()" track-class="home-gallery-grid em-home-gallery-track" :premium="true">
                    @foreach ($galleryHighlights as $highlight)
                        <x-public-photo :photo="$highlight['image']" :caption="$highlight['caption']" :preview="true" credit-prefix="Sumber foto ·"
                            class="activity-gallery-photo home-gallery-card" image-class="activity-gallery-image"
                            data-reveal />
                    @endforeach
                </x-card-carousel>
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
                <x-card-carousel :premium="true" id="potensi-cards" label="Kartu potensi Maniis" :count="count($potentials)">
                    @foreach ($potentials as $potential)
                        <article class="potential-card" data-reveal>
                            @if ($potential['photo'])
                                <x-public-photo :photo="$potential['photo']" image-class="aspect-[16/9] w-full object-cover" />
                            @endif
                            <div class="potential-card-text">
                                <h3><a
                                        href="{{ route('maniis') }}#{{ Str::slug($potential['title']) }}">{{ $potential['title'] }}</a>
                                </h3>
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
                <x-card-carousel :premium="true" id="program-cards" label="Kartu program" :count="$programs->count()">
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
                <x-card-carousel :premium="true" id="kegiatan-cards" label="Kartu kegiatan" :count="$activities->count()">
                    @foreach ($activities as $activity)
                        <div class="home-preview-card">
                            <x-activity-card :activity="$activity" :cover="$activityCovers[$activity->id] ?? null" heading="h3" />
                        </div>
                    @endforeach
                </x-card-carousel>
            </div>
        </section>
    @endif

    <section class="em-audience" aria-labelledby="audience-title">
        <div class="site-shell">
            <div class="em-section-lead" data-reveal>
                <div>
                    <p class="em-kicker">Mulai dari sini</p>
                    <h2 class="section-title" id="audience-title">Apa yang ingin Anda lakukan?</h2>
                </div>
                <p class="prose-copy">Temukan jalan yang sesuai dengan kebutuhan Anda.</p>
            </div>
            <div class="em-audience-grid">
                @foreach ([['Saya pelaku usaha', 'Kenalkan usaha dan temukan kesempatan pembinaan.', 'Pendaftaran UMKM/Pelaku Ekraf', 'contact'], ['Saya pelajar atau guru', 'Jelajahi ide proyek kreatif bersama sekolah.', null, 'creative.students'], ['Saya warga atau wisatawan', 'Kenali potensi dan karya lokal Maniis.', null, 'creative.map'], ['Saya calon mitra', 'Sampaikan gagasan kerja sama yang bermanfaat.', 'Kerja sama atau kolaborasi', 'contact']] as [$title, $description, $category, $route])
                    <a class="em-audience-link"
                        href="{{ $category ? route($route, ['kategori' => $category]) . '#form-kolaborasi' : route($route) }}">
                        <span><strong>{{ $title }}</strong><span>{{ $description }}</span></span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            aria-hidden="true">
                            <path d="m9 6 6 6-6 6" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-extra-tools />
    <x-community-tools />

    <section class="editorial-contact" aria-labelledby="contact-title">
        <div class="site-shell editorial-contact-grid" data-reveal>
            <div>
                <p class="em-home-contact-kicker">Dari Maniis, untuk masa depan.</p>
                <h2 id="contact-title" class="section-title">Kolaborasi dengan {{ $site['short_name'] }}</h2>
            </div>
            <div>
                <p>Warga, pelaku usaha, sekolah, komunitas, dan calon mitra dapat menyampaikan ide kerja sama melalui kanal
                    resmi pengurus.</p>
                <a class="editorial-link"
                    href="{{ route('contact') }}">{{ count($contacts) ? 'Lihat kanal resmi' : 'Informasi kontak' }}</a>
            </div>
        </div>
    </section>
@endsection
