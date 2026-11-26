@extends('layouts.public')
@section('content')
    <x-page-heading :title="$listing->name" :eyebrow="$listing->category" :description="$listing->kind === 'product' ? 'Detail produk lokal' : 'Profil toko dan usaha lokal'" />
    <div class="site-shell em-service-layout">
        <section class="em-tool-card">
            @if ($listing->photo_path)
                <img draggable="false" class="em-community-photo" src="{{ route('community.listing-photo', $listing) }}"
                    alt="{{ $listing->name }}">
            @endif
            <p>{{ $listing->village }}</p>
            <p class="em-prewrap">{{ $listing->description }}</p>
            @if ($listing->kind === 'product' && $listing->price !== null)
                <p class="em-price">Rp {{ number_format($listing->price, 0, ',', '.') }}</p>
            @endif
            @if ($business)
                <a class="editorial-link" href="{{ route('community.listing', $business) }}">Toko: {{ $business->name }}</a>
            @endif
            @if ($listing->whatsappUrl())
                <a class="button-primary" href="{{ $listing->whatsappUrl() }}" target="_blank"
                    rel="noopener noreferrer">Hubungi usaha</a>
            @endif
            <div class="em-qr"><img draggable="false" src="{{ route('community.listing-qr', $listing) }}" width="180" height="180"
                    alt="QR Code halaman {{ $listing->name }}">
                <p>Pindai untuk membuka halaman ini. QR menggunakan alamat website saat ini.</p><a
                    href="{{ route('community.listing-qr', $listing) }}" download="ekraf-produk.svg">Unduh QR</a>
            </div>
        </section>
        @if ($listing->kind === 'business')
            <section>
                <h2 class="section-title">Produk toko</h2>
                <div class="em-hub-grid">
                    @forelse($products as $product)
                        <a class="em-hub-card"
                        href="{{ route('community.listing', $product) }}">{{ $product->name }}</a>@empty<p>Belum ada
                            produk terhubung yang dipublikasikan.</p>
                    @endforelse
                </div>
            </section>
        @else
            <section class="em-tool-card">
                <h2 class="section-title">Ulasan pengunjung</h2>
                <p>Ulasan dimoderasi; bukan bukti pembelian terverifikasi.</p>
                @forelse($reviews as $review)
                    <article class="em-calendar-event"><strong>{{ $review->display_name }} ·
                            {{ $review->rating }}/5</strong>
                        <p>{{ $review->body }}</p>
                </article>@empty<p>Belum ada ulasan yang diterbitkan.</p>
                @endforelse
                {{ $reviews->links() }}
                @if (session('status'))
                    <p role="status">{{ session('status') }}</p>
                @endif
                @if ($errors->any())
                    <p role="alert">{{ $errors->first() }}</p>
                @endif
                <form method="post" action="{{ route('community.review', $listing) }}" class="em-admin-form">@csrf
                    <label for="review-name">Nama publik</label><input id="review-name" name="display_name" required
                        maxlength="80">
                    <label for="review-rating">Penilaian</label><select id="review-rating" name="rating">
                        @foreach (range(5, 1) as $rating)
                            <option value="{{ $rating }}">{{ $rating }}/5</option>
                        @endforeach
                    </select>
                    <label for="review-body">Ulasan berdasarkan pengalaman Anda</label>
                    <textarea id="review-body" name="body" maxlength="1000" required></textarea>
                    <label><input type="checkbox" name="consent" value="1" required> Saya setuju ulasan dan nama publik
                        ditampilkan setelah moderasi.</label><button class="button-primary">Kirim ulasan</button>
                </form>
            </section>
        @endif
    </div>
@endsection
