@extends('layouts.public')
@section('content')
    <x-page-heading title="Pusat Bantuan & Privasi" eyebrow="Informasi masyarakat"
        description="Panduan penggunaan portal dan perlindungan informasi pribadi." />
    <div class="site-shell em-service-layout">
        <section>
            <h2 class="section-title">Temukan bantuan</h2>
            <div class="em-hub-grid">
                <a class="em-hub-card" href="{{ route('services') }}#pencarian">
                    <h3>Cari informasi</h3>
                    <p>Cari program, kegiatan, dan subsektor.</p>
                </a>
                <a class="em-hub-card" href="{{ route('services') }}#faq">
                    <h3>Pertanyaan umum</h3>
                    <p>Baca jawaban yang tersedia.</p>
                </a>
                <a class="em-hub-card" href="{{ route('contact') }}#form-kolaborasi">
                    <h3>Hubungi pengurus</h3>
                    <p>Sampaikan pertanyaan atau koreksi data.</p>
                </a>
            </div>
        </section>
        <section>
            <h2 class="section-title">Informasi privasi</h2>
            <p>Kiriman komunitas, ulasan, dan survei disimpan untuk diperiksa pengurus. Kiriman dan ulasan hanya ditampilkan setelah moderasi. Foto diproses menjadi JPEG; jangan unggah data pribadi tanpa izin. Pemilik dapat menghubungi pengurus untuk meminta koreksi atau penarikan. Tautan verifikasi kartu dan sertifikat dapat dilihat oleh siapa pun yang memilikinya.</p>
            <div class="em-service-card">
                <p>Data usaha dan kontak publik hanya ditampilkan setelah pengurus menerbitkannya. Pemilik usaha dapat
                    meminta koreksi atau penarikan publikasi kepada pengurus.</p>
                <p>Formulir kolaborasi membuat draf pesan WhatsApp; pengiriman dilakukan oleh pengguna. WhatsApp memiliki
                    ketentuan privasinya sendiri.</p>
                <p>Preferensi tema, favorit, checklist usaha, dan progres belajar disimpan secara lokal pada browser. Portal tidak memerlukan
                    NIK atau kata sandi untuk mengakses halaman publik. Jangan mengirimkan data sensitif yang tidak
                    diperlukan.</p>
                <p>Pengurus dapat memperbarui kebijakan ini sebelum peluncuran publik. Untuk pertanyaan, gunakan kanal resmi
                    kontak.</p>
            </div>
        </section>
    </div>
@endsection
