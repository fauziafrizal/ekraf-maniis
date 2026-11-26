@extends('layouts.public')

@section('content')
    <x-page-heading title="Subsektor Ekonomi Kreatif" eyebrow="Ruang kreativitas Maniis" description="Temukan bidang usaha, karya, dan peluang kolaborasi yang sesuai dengan minat serta potensi Anda." />
    <section class="site-shell em-sector-layout sector-directory" aria-labelledby="daftar-subsektor">
        <div class="em-sector-overview">
            <div class="em-sector-count"><span class="eyebrow">Direktori kreatif</span><strong>{{ $subsectors->count() }}</strong><span>Subsektor terpublikasi</span></div>
            <div class="em-sector-overview-copy"><h2 class="section-title" id="daftar-subsektor">Temukan ruang berkarya.</h2><p class="prose-copy">Kenali setiap bidang dan contoh karya. Jelajahi layanan pembinaan untuk mengembangkan potensi Anda.</p><a class="button-secondary" href="{{ route('desk.index', absolute: false) }}">Akses layanan digital</a></div>
        </div>
        @if ($subsectors->isNotEmpty())
            <div data-em-filter>
                <div class="em-filter-tools em-sector-search" data-em-controls hidden>
                    <label for="subsector-search">Cari subsektor</label>
                    <div class="em-sector-search-input"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg><input id="subsector-search" type="search" data-em-query placeholder="Contoh: kuliner, kriya, musik..." autocomplete="off"></div>
                    <p data-em-count role="status"></p>
                </div>
                <ul class="em-sector-grid" role="list">
                    @foreach ($subsectors as $subsector)
                        <li id="subsektor-{{ $subsector->id }}" data-em-item class="em-sector-card">
                            <div class="em-sector-card-top">
                                <span class="em-sector-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"><path d="m12 3 9 5v8l-9 5-9-5V8l9-5Z"/><path d="m3 8 9 5 9-5M12 13v8"/></svg></span>
                                <span class="em-sector-number" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <h3>{{ $subsector->name }}</h3>
                            <p class="prose-copy">{{ $subsector->description }}</p>
                            @if (filled($subsector->examples))
                                <details class="em-sector-examples">
                                    <summary>Contoh kegiatan atau karya</summary>
                                    <p class="prose-copy whitespace-pre-line">{{ $subsector->examples }}</p>
                                </details>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <p data-em-empty class="em-service-note" hidden>Subsektor tidak ditemukan. Coba kata kunci lain.</p>
            </div>
        @else
            <div class="em-sector-empty"><h3>Direktori sedang disiapkan</h3><p>Informasi subsektor akan tersedia setelah dipublikasikan oleh pengurus.</p><a class="editorial-link" href="{{ route('contact', absolute: false) }}">Hubungi pengurus</a></div>
        @endif
        <details class="em-sector-poster">
            <summary>Infografis subsektor ekonomi kreatif <span>Lihat poster</span></summary>
            <figure><img draggable="false" src="{{ asset('images/subsektor.jpg') }}" alt="Potensi subsektor ekonomi kreatif Kecamatan Maniis" loading="lazy" decoding="async"></figure>
        </details>
    </section>
@endsection
