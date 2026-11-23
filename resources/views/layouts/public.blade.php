@php
    $site = $site ?? config('ekraf');
    $pageTitle = $pageTitle ?? $site['short_name'];
    $pageDescription = $pageDescription ?? $site['intro'];
    $title = $pageTitle === 'Beranda'
        ? $site['short_name'].' | '.$site['slogan']
        : $pageTitle.' | '.$site['short_name'];
    $navigation = [
        ['home', 'home', 'Beranda'],
        ['profile', 'profile', 'Profil'],
        ['maniis', 'maniis', 'Potensi Maniis'],
        ['subsectors', 'subsectors', 'Subsektor'],
        ['programs.index', 'programs.*', 'Program'],
        ['activities.index', 'activities.*', 'Kegiatan'],
        ['contact', 'contact', 'Kontak'],
    ];
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173f35">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $pageDescription }}">
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
        <link rel="icon" href="{{ $logo['url'] }}">
    @endif
    <script>
        (() => {
            let savedTheme;
            try {
                savedTheme = localStorage.getItem('ekraf-theme');
            } catch {}

            document.documentElement.dataset.theme = savedTheme === 'dark' || savedTheme === 'light'
                ? savedTheme
                : (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main-content">Lewati ke konten</a>
    <header class="site-header" data-site-header>
        <div class="site-shell header-inner">
            <a class="wordmark" href="{{ route('home', absolute: false) }}">
                @if (!empty($logo))
                    <img src="{{ $logo['url'] }}" width="{{ $logo['width'] }}" height="{{ $logo['height'] }}" alt="" class="brand-logo">
                @endif
                <span>
                    <strong>{{ $site['short_name'] }}</strong>
                    <span class="wordmark-caption">{{ $site['name'] }}</span>
                </span>
            </a>
            <nav class="desktop-nav" aria-label="Menu utama">
                @foreach ($navigation as [$name, $pattern, $label])
                    <a class="nav-link" href="{{ route($name, absolute: false) }}" @if (request()->routeIs($pattern)) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
            <details class="mobile-menu" data-mobile-menu>
                <summary class="menu-toggle">
                    <span>Menu</span>
                    <span class="menu-icon" aria-hidden="true"></span>
                </summary>
                <nav class="mobile-nav" aria-label="Menu utama">
                    @foreach ($navigation as [$name, $pattern, $label])
                        <a class="nav-link" href="{{ route($name, absolute: false) }}" @if (request()->routeIs($pattern)) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                </nav>
            </details>
        </div>
        <div class="site-preferences" data-site-preferences hidden>
            <div class="site-shell site-preferences-inner" role="group" aria-label="Pengaturan situs">
                <button type="button" class="site-preference" data-music-toggle aria-pressed="false">
                    <span class="preference-icon" aria-hidden="true">♪</span>
                    <span>Musik</span>
                </button>
                <span class="sr-only" data-music-status role="status"></span>
                <button type="button" class="site-preference" data-theme-toggle aria-pressed="false">
                    <span class="preference-icon preference-icon--theme" aria-hidden="true"></span>
                    <span>Tema gelap</span>
                </button>
            </div>
        </div>
    </header>

    <main id="main-content" tabindex="-1" data-page="{{ request()->route()?->getName() }}">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="site-shell footer-grid">
            <div>
                <a class="footer-brand" href="{{ route('home', absolute: false) }}">{{ $site['short_name'] }}</a>
                <p>{{ $site['name'] }} · {{ $site['period'] }}</p>
                <p>{{ $site['location'] }}</p>
                @if (!empty($governmentLogo))
                    <img src="{{ $governmentLogo['url'] }}" alt="{{ $governmentLogo['alt'] }}" width="{{ $governmentLogo['width'] }}" height="{{ $governmentLogo['height'] }}" loading="lazy" class="government-logo">
                @endif
            </div>
            <div>
                <p class="footer-slogan">{{ $site['slogan'] }}</p>
                <nav class="footer-links" aria-label="Tautan kaki halaman">
                    <a href="{{ route('profile', absolute: false) }}#pengurus">Struktur Pengurus</a>
                    <a href="{{ route('programs.index', absolute: false) }}">Program</a>
                    <a href="{{ route('contact', absolute: false) }}">Kontak</a>
                </nav>
                <p class="footer-music-credit">
                    <a href="https://pixabay.com/id/music/dunia-musik-degung-sunda-free-for-use-434888/">Musik Degung Sunda</a> oleh fiikuri via Pixabay
                </p>
            </div>
        </div>
    </footer>
    <audio data-site-music src="{{ route('site.music', absolute: false) }}" preload="none" loop></audio>
</body>
</html>
