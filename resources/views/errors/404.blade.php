@extends('layouts.public', [
    'pageTitle' => 'Halaman tidak ditemukan',
    'pageDescription' => 'Halaman yang Anda cari tidak tersedia.',
])

@section('content')
    <section class="site-shell py-16 sm:py-24">
        <h1 class="page-title">Halaman tidak ditemukan</h1>
        <p class="prose-copy mt-5">Periksa kembali alamat halaman atau jelajahi informasi yang tersedia dari beranda.</p>
        <a href="{{ route('home', absolute: false) }}" class="button-primary mt-8">Kembali ke Beranda</a>
    </section>
@endsection
