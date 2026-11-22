@extends('layouts.public')

@section('content')
    <x-page-heading :title="$program->title" :description="$program->summary" eyebrow="Program">
        <x-program-status :status="$program->status" />
    </x-page-heading>

    <div class="site-shell grid gap-10 py-12 sm:py-16 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,0.65fr)] lg:gap-14 lg:py-20">
        <article class="min-w-0">
            <a class="inline-flex font-semibold text-pine underline underline-offset-4" href="{{ route('programs.index') }}">Kembali ke daftar program</a>
            <h2 class="section-title mt-7">Tentang program</h2>
            <x-content-paragraphs :text="$program->body" class="mt-5" />

            <div class="mt-8 border-t border-[#dfe4d8] pt-6">
                <h2 class="text-xl font-semibold text-pine">Peluang kolaborasi</h2>
                <p class="prose-copy mt-2">Sampaikan ide atau pertanyaan tentang program ini kepada pengurus.</p>
                <a class="mt-3 inline-flex font-semibold text-pine underline underline-offset-4" href="{{ route('contact') }}">Kontak {{ $site['short_name'] }}</a>
            </div>
        </article>

        <section class="h-fit rounded-2xl border border-[#dfe4d8] bg-[#f9faf5] p-6 sm:p-8" aria-labelledby="program-information">
            <h2 id="program-information" class="text-xl font-semibold text-pine">Informasi program</h2>
            <dl class="mt-5 divide-y divide-[#dfe4d8]">
                <div class="pb-4">
                    <dt class="font-semibold text-pine">Sasaran</dt>
                    <dd class="prose-copy mt-1 wrap-anywhere">{{ $program->audience }}</dd>
                </div>
                @if ($program->starts_at)
                    <div class="py-4">
                        <dt class="font-semibold text-pine">Jadwal</dt>
                        <dd class="prose-copy mt-1 wrap-anywhere">
                            <time datetime="{{ $program->starts_at->toIso8601String() }}">{{ $program->starts_at->translatedFormat('j F Y, H.i') }} WIB</time>
                        </dd>
                    </div>
                @endif
                @if (filled($program->location))
                    <div class="py-4">
                        <dt class="font-semibold text-pine">Lokasi</dt>
                        <dd class="prose-copy mt-1 wrap-anywhere">{{ $program->location }}</dd>
                    </div>
                @endif
            </dl>

            @if ($registrationUrl)
                <a class="button-primary mt-4" href="{{ $registrationUrl }}">Daftar</a>
            @endif
        </section>
    </div>
@endsection
