@extends('layouts.public')

@section('content')
    <x-page-heading
        class="section-masthead section-masthead--activities"
        title="Kegiatan"
        :description="'Dokumentasi kegiatan dan karya yang telah dipublikasikan oleh '.$site['short_name'].'.'"
    >
        <a class="button-primary" href="#galeri">Lihat Galeri</a>
    </x-page-heading>

    @php($galleryOnly = $activities->isEmpty() && $photos->isNotEmpty())

    <div @class(['site-shell section-body activity-layout', 'activity-layout--gallery-only' => $galleryOnly])>
        @unless ($galleryOnly)
            <section aria-labelledby="activity-list-title">
                <h2 id="activity-list-title" class="section-title activity-section-title">Dokumentasi kegiatan</h2>
                @if ($activities->isEmpty())
                    <p class="activity-empty" data-reveal>Dokumentasi kegiatan akan ditambahkan di sini.</p>
                @else
                    <div class="activity-journal">
                        @foreach ($activities as $activity)
                            <div class="activity-journal-entry" data-reveal>
                                <x-activity-card :activity="$activity" :cover="$activityCovers[$activity->id] ?? null" heading="h3" />
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @endunless

        <section id="galeri" class="activity-gallery" aria-labelledby="gallery-title">
            <h2 id="gallery-title" class="section-title activity-section-title">Galeri</h2>
            @if ($photos->isEmpty())
                <p class="activity-empty" data-reveal>Foto kegiatan akan ditambahkan setelah siap dipublikasikan.</p>
            @else
                <div class="activity-gallery-grid">
                    @foreach ($photos as $photo)
                        @php($asset = $photoAssets[$photo->id])
                        <x-public-photo :photo="$asset" :caption="$photo->caption"
                            :preview="true" credit-prefix="Sumber foto ·"
                            class="activity-gallery-photo"
                            :image-class="\Illuminate\Support\Arr::toCssClasses([
                                'activity-gallery-image',
                                'activity-gallery-image--wide' => $asset['width'] / $asset['height'] >= 1.7,
                            ])" data-reveal />
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    @if ($photos->isNotEmpty())
        <x-gallery-preview-dialog />
    @endif
@endsection
