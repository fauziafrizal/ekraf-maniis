@extends('layouts.public')

@section('content')
    <article>
        <header class="activity-detail-hero">
            <div class="site-shell">
                <a class="detail-return" href="{{ route('activities.index') }}">Kembali ke daftar kegiatan</a>
                <div class="activity-detail-masthead">
                    <div>
                        <p class="detail-kicker">Kegiatan</p>
                        <h1 class="detail-title">{{ $activity->title }}</h1>
                        <p class="detail-lead">{{ $activity->summary }}</p>
                    </div>
                    <time class="activity-detail-date"
                        datetime="{{ $activity->event_date->toDateString() }}">{{ $activity->event_date->translatedFormat('j F Y') }}</time>
                </div>
            </div>
        </header>

        <div class="site-shell activity-detail-content">
            @if (isset($activityCovers[$activity->id]))
                <x-public-photo :photo="$activityCovers[$activity->id]" :priority="true" class="activity-detail-cover"
                    image-class="activity-detail-cover-image" />
            @endif
            <x-content-paragraphs :text="$activity->body" class="detail-body activity-detail-story" data-reveal />
        </div>

        @if ($photos->isNotEmpty())
            <section class="activity-detail-gallery" aria-labelledby="documentation-title">
                <div class="site-shell">
                    <h2 id="documentation-title" class="section-title" data-reveal>Foto dokumentasi</h2>
                    <div class="activity-detail-gallery-grid">
                        @foreach ($photos as $photo)
                            <x-public-photo :photo="$photoAssets[$photo->id]" :caption="$photo->caption" data-reveal />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </article>
@endsection
