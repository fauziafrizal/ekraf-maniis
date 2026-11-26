@extends('layouts.public')
@section('content')
    <x-page-heading title="Verifikasi Dokumen Ekraf"
        description="Dokumen tercatat dan berstatus aktif pada sistem Ekraf Maniis." />
    <div class="site-shell em-service-layout">
        <article class="em-tool-card em-credential">
            <p class="em-kicker">{{ $credential->kind === 'certificate' ? 'E-Sertifikat' : 'Kartu Anggota Digital' }}</p>
            <h2 class="section-title">{{ $credential->title }}</h2>
            <p>Nomor: {{ $credential->number }}</p>
            <h3>{{ $credential->recipient }}</h3>
            <p>Diterbitkan: {{ $credential->issued_on }}</p>
            <p>Siapa pun dengan tautan unik dapat melihat informasi verifikasi. Dokumen ini merupakan catatan internal
                organisasi, bukan identitas pemerintah atau sertifikasi profesi.</p>
            <img draggable="false" width="180" height="180" src="{{ route('community.credential-qr', $credential->token) }}"
                alt="QR verifikasi dokumen"><button class="button-primary" data-print hidden> Cetak dokumen </button>
        </article>
    </div>
@endsection
