@extends('layouts.public')
@section('content')
    <x-page-heading title="Notifikasi Kegiatan"
        description="Informasi agenda mendatang. Aktifkan pengingat di browser jika didukung." />
    <div class="site-shell em-service-layout">
        <section class="em-tool-card">
            <button class="button-primary" type="button" data-alert-permission hidden>Aktifkan notifikasi browser</button>
            <p data-alert-status role="status">Gunakan kalender perangkat untuk pengingat yang tetap berjalan saat website
                ditutup.</p>
            @forelse($programs as $program)
                <article class="em-calendar-event" data-event-title="{{ $program->title }}"
                    data-event-time="{{ $program->starts_at->toIso8601String() }}">
                    <h3>{{ $program->title }}</h3>
                    <p>{{ $program->starts_at->translatedFormat('j F Y, H.i') . ' WIB' }}</p><a class="editorial-link"
                        href="{{ route('creative.calendar.ics', $program->slug) }}">Simpan ke kalender</a>
            </article>@empty<p>Belum ada agenda mendatang yang dipublikasikan.</p>
            @endforelse
            <p class="em-service-note">Notifikasi browser ini hanya berjalan ketika halaman ini terbuka; bukan push
                notification latar belakang.</p>
        </section>
    </div>
@endsection
