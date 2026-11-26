@extends('layouts.public')
@section('content')
    <x-page-heading title="Peta dan Potensi Maniis" eyebrow="Jelajahi daerah"
        description="Kenali kekayaan alam dan peluang ekonomi kreatif setempat." />
    <div class="site-shell em-service-layout">
        <section>
            <h2 class="section-title">Eksplorasi wilayah</h2>
            <div class="em-hub-grid">
                @foreach ($potentials as $potential)
                    <article class="em-service-card">
                        <h3>{{ $potential['title'] }}</h3>
                        <p>{{ $potential['body'] }}</p><a class="editorial-link"
                            href="{{ route('maniis') }}#{{ Str::slug($potential['title']) }}">Baca potensi</a>
                    </article>
                @endforeach
            </div>
            <a class="button-primary" href="https://www.google.com/maps/search/?api=1&query=Kecamatan+Maniis+Purwakarta"
                target="_blank" rel="noopener noreferrer">Buka peta Kecamatan Maniis</a>
            <a class="editorial-link" href="https://www.google.com/maps/search/?api=1&query=Waduk+Cirata" target="_blank"
                rel="noopener noreferrer">Cari Waduk Cirata di peta</a>
            <p class="em-service-note">Titik usaha dan destinasi spesifik akan ditambahkan setelah lokasi diverifikasi oleh
                pengurus. Periksa kondisi, akses, dan izin sebelum berkunjung.</p>
        </section>
    </div>
@endsection
