@extends('layouts.public')

@section('content')
    <x-page-heading :title="$activity->title" :description="$activity->summary" eyebrow="Kegiatan">
        <time datetime="{{ $activity->event_date->toDateString() }}" class="font-semibold text-[#365a4b]">{{ $activity->event_date->translatedFormat('j F Y') }}</time>
    </x-page-heading>

    <div class="site-shell py-12 sm:py-16 lg:py-20">
        <article class="max-w-4xl">
            <a class="inline-flex font-semibold text-pine underline underline-offset-4" href="{{ route('activities.index') }}">Kembali ke daftar kegiatan</a>

            @if (isset($activityCovers[$activity->id]))
                <x-public-photo :photo="$activityCovers[$activity->id]" class="mt-7 overflow-hidden rounded-2xl border border-[#dfe4d8] bg-white" image-class="aspect-[16/9] w-full object-cover" />
            @endif
            <x-content-paragraphs :text="$activity->body" class="mt-7 max-w-3xl" />
        </article>

        @if ($photos->isNotEmpty())
            <section class="mt-12 border-t border-[#dfe4d8] pt-10 sm:mt-16 sm:pt-12" aria-labelledby="documentation-title">
                <h2 id="documentation-title" class="section-title">Foto dokumentasi</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($photos as $photo)
                        <x-public-photo :photo="$photoAssets[$photo->id]" :caption="$photo->caption" class="overflow-hidden rounded-2xl border border-[#dfe4d8] bg-white" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
