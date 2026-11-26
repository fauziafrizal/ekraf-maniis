@extends('layouts.public')
@section('content')
    <x-page-heading :title="$pageTitle" eyebrow="Smart Creative Hub" :description="$pageDescription">
        <a class="button-primary"
            href="{{ route('contact', ['kategori' => 'Pendaftaran UMKM/Pelaku Ekraf']) }}#form-kolaborasi">Ajukan
            pendataan</a>
    </x-page-heading>
    <div class="site-shell em-service-layout" data-em-filter>
        <section>
            <h2 class="section-title">{{ $kind === 'product' ? 'Katalog produk' : 'Daftar usaha' }}</h2>
            <p class="prose-copy">Hanya data yang diterbitkan oleh pengurus ditampilkan. Transaksi dan konfirmasi harga
                dilakukan langsung dengan penjual.</p>
            @if ($listings->isNotEmpty())
                <div data-em-controls hidden class="em-filter-tools">
                    <label for="listing-search">Cari nama, kategori, atau desa</label>
                    <input type="search" id="listing-search" data-em-query placeholder="Cari di katalog">
                    <label for="listing-category">Kategori</label>
                    <select id="listing-category" data-em-status>
                        <option value="">Semua kategori</option>
                        @foreach ($listings->pluck('category')->unique()->sort() as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                    <p data-em-count role="status"></p>
                </div>
                <div class="em-hub-grid">
                    @foreach ($listings as $item)
                        <article class="em-service-card" data-em-item="{{ $item->category }}">
                            <p class="em-listing-category">{{ $item->category }} @if ($item->village)
                                    · {{ $item->village }}
                                @endif
                            </p>
                            @if ($item->photo_path)
                                <img draggable="false" class="em-community-photo" src="{{ route('community.listing-photo', $item) }}"
                                    alt="{{ $item->name }}" loading="lazy">
                            @endif
                            <h3><a href="{{ route('community.listing', $item) }}">{{ $item->name }}</a></h3>
                            <a class="editorial-link" href="{{ route('community.listing', $item) }}">Detail dan QR</a>
                            <p>{{ $item->description }}</p>
                            @if ($kind === 'product')
                                <p class="em-price">
                                    {{ $item->price !== null ? 'Rp ' . number_format($item->price, 0, ',', '.') : 'Hubungi penjual untuk harga' }}
                                </p>
                            @endif
                            @if ($item->whatsappUrl())
                                <a class="button-primary" href="{{ $item->whatsappUrl() }}" target="_blank"
                                    rel="noopener noreferrer">Hubungi {{ $kind === 'product' ? 'penjual' : 'usaha' }}</a>
                            @endif
                            <button class="em-favorite" type="button" data-em-favorite="{{ $item->id }}" hidden
                                aria-pressed="false">Simpan favorit</button>
                        </article>
                    @endforeach
                </div>
                <p class="em-service-note" data-em-empty hidden>Data tidak ditemukan.</p>
            @else
                <p class="em-service-note">Belum ada {{ $kind === 'product' ? 'produk' : 'usaha' }} yang diterbitkan.
                    Pelaku kreatif dapat mengajukan pendataan melalui pengurus.</p>
            @endif
            <a class="editorial-link" href="{{ route('creative.hub') }}">Kembali ke Smart Creative Hub</a>
        </section>
    </div>
@endsection
