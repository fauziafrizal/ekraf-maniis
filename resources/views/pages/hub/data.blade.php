@extends('layouts.public')
@section('content')
    <x-page-heading title="Ekraf Data Center" eyebrow="Data publik"
        description="Ringkasan data yang telah dipublikasikan di portal Ekraf Maniis." />
    <div class="site-shell em-service-layout">
        <section>
            <h2 class="section-title">Statistik portal</h2>
            <div class="em-hub-grid">
                @foreach ($metrics as $label => $value)
                    <article class="em-service-card em-metric"><strong>{{ number_format($value, 0, ',', '.') }}</strong>
                        <h3>{{ $label }}</h3>
                    </article>
                @endforeach
            </div>
            <p class="em-service-note">Angka merupakan jumlah data publik di website, bukan jumlah keseluruhan pelaku ekraf
                Kecamatan Maniis.</p>
            <a class="editorial-link" href="{{ route('creative.csv') }}">Unduh ringkasan CSV</a>
        </section>
        <section>
            <h2 class="section-title">Persebaran usaha terdaftar</h2>
            @forelse($villages as $village)
                <div class="em-metric-row">
                    <span>{{ $village->village ?? 'Desa belum dicatat' }}</span><strong>{{ $village->total }}</strong></div>
                <meter min="0" max="{{ max(1, $metrics['Usaha publik']) }}"
                    value="{{ $village->total }}">{{ $village->total }}</meter>
            @empty <p class="em-service-note">Data persebaran belum tersedia.</p>
            @endforelse
        </section>
    </div>
@endsection
