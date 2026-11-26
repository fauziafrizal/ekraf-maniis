@extends('layouts.public')

@section('content')
    <section class="em-hub-hero" aria-labelledby="hub-title">
        <div class="site-shell em-hub-hero-inner">
            <p class="em-kicker">Ekraf Maniis · {{ $site['period'] }}</p>
            <h1 id="hub-title">Ruang kreatif.<br><em>Tanpa batas.</em></h1>
            <p>Satu pintu untuk mengenal karya lokal, belajar, mengakses layanan, dan berkolaborasi.</p>
            <a class="button-primary" href="#hub-modules">Jelajahi ekosistem</a>
        </div>
    </section>

    <section class="em-experience-section" id="hub-modules" aria-labelledby="hub-modules-title">
        <div class="site-shell">
            <div class="em-section-lead" data-reveal>
                <div>
                    <p class="em-kicker">Smart Creative Hub</p>
                    <h2 class="section-title" id="hub-modules-title">Pilih ruang kreatif Anda.</h2>
                </div>
                <p class="prose-copy">Delapan ruang digital untuk menjelajahi potensi dan berkolaborasi.</p>
            </div>
            <x-experience-grid />
            <a class="editorial-link" href="{{ route('login') }}">Akses khusus pengurus</a>
        </div>
    </section>
    <x-extra-tools />
    <x-community-tools />
@endsection
