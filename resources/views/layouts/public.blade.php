@php
    $site = $site ?? config('ekraf');
    $logo = $logo ?? \App\Support\PublicSite::image($site['assets']['logo'] ?? null);
    $logoSource = $logo ? \App\Support\PublicSite::logoSource($logo) : null;

    $pageTitle = $pageTitle ?? $site['short_name'];
    $pageDescription = $pageDescription ?? $site['intro'];
    $title =
        $pageTitle === 'Beranda'
            ? $site['short_name'] . ' | ' . $site['slogan']
            : $pageTitle . ' | ' . $site['short_name'];
    $navigation = [
        ['home', 'home', 'Beranda'],
        ['profile', 'profile', 'Profil'],
        ['maniis', 'maniis', 'Potensi Maniis'],
        ['subsectors', 'subsectors', 'Subsektor'],
        ['programs.index', 'programs.*', 'Program'],
        ['activities.index', 'activities.*', 'Kegiatan'],
        ['services', 'services', 'Layanan'],
        ['creative.hub', 'creative.*', 'Creative Hub'],
        ['contact', 'contact', 'Kontak'],
    ];
@endphp
<!doctype html>
<html lang="id" data-ekraf-ambient>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173f35">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    @if (request()->routeIs('login', 'creative.admin', 'office.*', 'community.verify'))
        <meta name="robots" content="noindex, nofollow">
    @endif
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $site['short_name'] }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    @if (!empty($canonical))
        <link rel="canonical" href="{{ $canonical }}">
        <meta property="og:url" content="{{ $canonical }}">
    @endif
    @if (!empty($metaImage))
        <meta property="og:image" content="{{ $metaImage['url'] }}">
        <meta property="og:image:alt" content="{{ $metaImage['alt'] }}">
        <meta property="og:image:width" content="{{ $metaImage['width'] }}">
        <meta property="og:image:height" content="{{ $metaImage['height'] }}">
    @endif
    @if (!empty($logo))
        <link rel="icon" href="{{ asset('images/logo-header.png') }}" type="image/png">
    @endif
    <script nonce="{{ Vite::cspNonce() }}">
        (() => {
            let savedTheme;
            try {
                savedTheme = localStorage.getItem('ekraf-theme');
            } catch {}

            document.documentElement.dataset.theme = savedTheme === 'dark' || savedTheme === 'light' ?
                savedTheme :
                (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.style.colorScheme = document.documentElement.dataset.theme;
        })();
    </script>
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/ekraf-icon-192.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <a class="skip-link" href="#main-content">Lewati ke konten</a>
    <header class="site-header" data-site-header>
        <div class="site-shell header-inner">
            <a class="wordmark" href="{{ route('home', absolute: false) }}">
                @if (!empty($logo))
                    <img draggable="false" src="{{ $logoSource }}" width="{{ $logo['width'] }}" height="{{ $logo['height'] }}"
                        alt="" class="brand-logo" loading="eager" decoding="sync" fetchpriority="high">
                @endif
                <span>
                    <strong>{{ $site['short_name'] }}</strong>
                    <span class="wordmark-caption">{{ $site['name'] }}</span>
                </span>
            </a>
            <nav class="desktop-nav" aria-label="Menu utama">
                @foreach ($navigation as [$name, $pattern, $label])
                    <a class="nav-link" href="{{ route($name, absolute: false) }}"
                        @if (request()->routeIs($pattern)) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
            <details class="mobile-menu" data-mobile-menu>
                <summary class="menu-toggle">
                    <span>Menu</span>
                    <span class="menu-icon" aria-hidden="true"></span>
                </summary>
                <nav class="mobile-nav" aria-label="Menu utama">
                    @foreach ($navigation as [$name, $pattern, $label])
                        <a class="nav-link" href="{{ route($name, absolute: false) }}"
                            @if (request()->routeIs($pattern)) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                </nav>
            </details>
        </div>
        <div class="site-preferences" data-site-preferences>
            <div class="site-shell site-preferences-inner" role="group" aria-label="Pengaturan situs">
                <a class="em-header-search" data-visitor-search href="{{ route('services') }}#pencarian" aria-label="Cari informasi"><svg
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="10.5" cy="10.5" r="6.5" />
                        <path d="m16 16 4 4" />
                    </svg>Cari</a>
                <button type="button" class="site-preference" data-music-toggle aria-pressed="false" disabled>
                    <span class="preference-icon" aria-hidden="true">♪</span>
                    <span>Musik</span>
                </button>
                <span class="sr-only" data-music-status role="status"></span>
                <button type="button" class="site-preference" data-theme-toggle aria-pressed="false" disabled>
                    <span class="preference-icon preference-icon--theme" aria-hidden="true"></span>
                    <span>Tema gelap</span>
                </button>
            </div>
        </div>
        <div class="em-reading-progress" aria-hidden="true"><span data-em-reading-progress></span></div>
    </header>
    <script nonce="{{ Vite::cspNonce() }}">
        (() => {
            const header = document.querySelector('[data-site-header]');
            if (header) document.documentElement.style.setProperty('--em-header-height', Math.ceil(header.getBoundingClientRect().height) + 'px');
        })();
    </script>

    <main id="main-content" tabindex="-1" data-page="{{ request()->route()?->getName() }}">
        <div class="em-home-ambient" aria-hidden="true"></div>
        @if (config('ekraf.static_preview') && request()->routeIs('desk.index', 'community.index', 'community.feedback', 'community.studio', 'community.research', 'login'))
            <x-page-heading :title="$pageTitle" eyebrow="Versi publik GitHub Pages" description="Layanan ini membutuhkan server Laravel aktif." />
            <section class="site-shell em-service-layout"><div class="em-service-card">
                <h2 class="section-title">Akses layanan online</h2>
                <p>Versi situs ini menampilkan informasi publik. Pengajuan, pelacakan, kiriman komunitas, AI eksternal, dan masuk pengurus belum dapat dijalankan di GitHub Pages. Jangan memasukkan data pribadi atau kata sandi pada halaman statis.</p>
                <a class="button-primary" href="{{ route('contact', absolute: false) }}">Hubungi pengurus</a>
                <a class="button-secondary" href="{{ route('creative.hub', absolute: false) }}">Kembali ke Creative Hub</a>
            </div></section>
        @else
            @yield('content')
        @endif
    </main>

    <footer class="site-footer" data-site-footer>
        <details class="footer-details">
            <summary class="site-shell footer-bar">
                <span class="footer-bar-identity"><strong>{{ $site['short_name'] }} ·
                        {{ $site['period'] }}</strong><span>{{ $site['slogan'] }}</span></span>
                <span class="footer-bar-toggle">Info &amp; tautan</span>
            </summary>
            <div class="site-shell footer-grid">
                <div>
                    <a class="footer-brand" href="{{ route('home', absolute: false) }}">{{ $site['short_name'] }}</a>
                    <p>{{ $site['name'] }} · {{ $site['period'] }}</p>
                    <p>{{ $site['location'] }}</p>
                    @if (!empty($governmentLogo))
                        <img draggable="false" src="{{ $governmentLogo['url'] }}" alt="{{ $governmentLogo['alt'] }}"
                            width="{{ $governmentLogo['width'] }}" height="{{ $governmentLogo['height'] }}"
                            loading="lazy" class="government-logo">
                    @endif
                </div>
                <div>
                    <p class="footer-slogan">{{ $site['slogan'] }}</p>
                    <nav class="footer-links" aria-label="Tautan kaki halaman">
                        <a href="{{ route('profile', absolute: false) }}#pengurus">Struktur Pengurus</a>
                        <a href="{{ route('programs.index', absolute: false) }}">Program</a>
                        <a href="{{ route('services', absolute: false) }}">Pusat Layanan</a>
                        <a href="{{ route('contact', absolute: false) }}">Kontak</a>
                    </nav>
                    <button class="em-share" type="button" data-em-install hidden>Instal aplikasi</button>
                    <button class="em-share" type="button" data-em-share hidden>Bagikan halaman</button>
                    <p class="em-share-result" data-em-share-result role="status"></p>
                    <p class="footer-music-credit">
                        <a href="https://pixabay.com/id/music/dunia-musik-degung-sunda-free-for-use-434888/">Musik
                            Degung Sunda</a> oleh fiikuri via Pixabay
                    </p>
                </div>
            </div>
        </details>
    </footer>
    @unless (request()->routeIs('login', 'creative.admin', 'office.*', 'community.verify', 'desk.queue'))
        <x-ekraf-assistant />
    @endunless
    <button type="button" class="em-top" aria-label="Kembali ke atas" data-em-top hidden>↑</button>
    <x-visitor-navigator />
    <audio data-site-music src="{{ route('site.music', absolute: false) }}" preload="none" loop></audio>
</body>

</html>
