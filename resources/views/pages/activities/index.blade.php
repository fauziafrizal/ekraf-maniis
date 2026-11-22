@extends('layouts.public')

@section('content')
    <x-page-heading title="Kegiatan" :description="'Dokumentasi kegiatan dan karya yang telah dipublikasikan oleh '.$site['short_name'].'.'">
        <a class="button-primary" href="#galeri">Lihat Galeri</a>
    </x-page-heading>

    <div class="site-shell py-12 sm:py-16 lg:py-20">
        <section aria-label="Daftar kegiatan">
            @if ($activities->isEmpty())
                <x-empty-state>Dokumentasi kegiatan akan ditambahkan di sini.</x-empty-state>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($activities as $activity)
                        <x-activity-card :activity="$activity" :cover="$activityCovers[$activity->id] ?? null" />
                    @endforeach
                </div>
            @endif
        </section>

        <section id="galeri" class="mt-12 scroll-mt-20 border-t border-[#dfe4d8] pt-10 sm:mt-16 sm:pt-12" aria-labelledby="gallery-title">
            <h2 id="gallery-title" class="section-title">Galeri</h2>
            @if ($photos->isEmpty())
                <x-empty-state class="mt-6">Foto kegiatan akan ditambahkan setelah siap dipublikasikan.</x-empty-state>
            @else
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($photos as $photo)
                        <x-public-photo :photo="$photoAssets[$photo->id]" :caption="$photo->caption" class="overflow-hidden rounded-2xl border border-[#dfe4d8] bg-white" />
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
