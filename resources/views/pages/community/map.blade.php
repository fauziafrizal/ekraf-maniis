@extends('layouts.public')
@section('content')
    <x-page-heading title="Peta UMKM & Tur Virtual"
        description="Lokasi usaha yang diizinkan untuk publikasi dan ruang eksplorasi panorama." />
    <div class="site-shell em-service-layout">
        <section class="em-tool-card">
            <h2 class="section-title">Peta persebaran usaha</h2>
            <p>Lokasi berasal dari koordinat yang dimasukkan pengurus. Peta dasar dimuat dari OpenStreetMap jika Anda
                memilih untuk membukanya.</p>
            @if ($businesses->isNotEmpty())
                <button type="button" class="button-primary" data-map-load hidden>Buka peta interaktif</button>
                <div data-ekraf-map class="em-map" hidden></div>
                @foreach ($businesses as $business)
                    <article class="em-calendar-event" data-map-point data-lat="{{ $business->latitude }}"
                        data-lng="{{ $business->longitude }}">
                        <h3><a href="{{ route('community.listing', $business) }}">{{ $business->name }}</a></h3>
                        <p>{{ $business->village }}</p><a
                            href="https://www.openstreetmap.org/?mlat={{ $business->latitude }}&amp;mlon={{ $business->longitude }}#map=15/{{ $business->latitude }}/{{ $business->longitude }}"
                            rel="noopener noreferrer" target="_blank">Lihat lokasi</a>
                    </article>
                @endforeach
            @else<p class="em-service-note">Belum ada lokasi usaha terverifikasi untuk publikasi.</p>
            @endif
        </section>
        <section class="em-tool-card">
            <h2 class="section-title">Virtual Tour 360°</h2>
            @php($panorama = public_path('images/panorama-maniis.jpg'))
            @if (is_file($panorama))
                <button type="button" class="button-primary" data-panorama-load hidden>Mulai tur 360°</button>
                <div class="em-map" data-panorama="{{ asset('images/panorama-maniis.jpg') }}" hidden></div>
                <p>Geser tampilan untuk melihat sekitar.</p>
            @else<p class="em-service-note">Panorama 360° asli belum tersedia. Pengurus perlu menyiapkan foto
                    equirectangular berizin agar tur dapat digunakan.</p><a class="editorial-link"
                    href="{{ route('maniis') }}">Jelajahi potensi Maniis</a>
            @endif
        </section>
    </div>
@endsection
