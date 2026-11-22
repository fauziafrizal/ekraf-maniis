@extends('layouts.public')

@section('content')
    <x-page-heading :title="'Profil '.$site['short_name']" eyebrow="Tentang kami" :description="$site['name'].' · '.$site['period']">
        <a class="button-primary" href="#pengurus">Struktur Pengurus</a>
    </x-page-heading>

    <div class="site-shell py-12 sm:py-16 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)] lg:gap-16">
            <section aria-labelledby="about-title">
                <h2 id="about-title" class="section-title">Tentang {{ $site['short_name'] }}</h2>
                <div class="prose-copy mt-5 max-w-2xl space-y-4">
                    @foreach ($site['profile'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
                <p class="profile-slogan">{{ $site['slogan'] }}</p>
            </section>

            <section class="profile-chair" aria-labelledby="chair-title">
                @if ($chair && isset($memberPhotos[$chair->id]))
                    <x-public-photo :photo="$memberPhotos[$chair->id]" image-class="aspect-[16/10] w-full bg-[#eef1e8] object-cover [object-position:50%_32%]" />
                @endif
                <div class="p-6 sm:p-8">
                    <h2 id="chair-title" class="section-title">Profil ketua</h2>
                    @if ($chair)
                        <p class="mt-4 text-xl font-semibold leading-snug wrap-anywhere text-pine">{{ $chair->name }}</p>
                        @if (filled($chair->bio))
                            <x-content-paragraphs :text="$chair->bio" class="mt-4" />
                        @else
                            <p class="prose-copy mt-3">Biografi ketua sedang disiapkan.</p>
                        @endif
                    @else
                        <p class="prose-copy mt-4">Profil ketua sedang disiapkan.</p>
                    @endif
                </div>
            </section>
        </div>

        <section id="pengurus" class="mt-12 scroll-mt-20 border-t border-[#dfe4d8] pt-10 sm:mt-16 sm:pt-12" aria-labelledby="board-title">
            <h2 id="board-title" class="section-title">Struktur Pengurus</h2>
            <p class="prose-copy mt-3">Masa kepengurusan {{ $site['period'] }}</p>
            @if ($members->isEmpty())
                <x-empty-state class="mt-6">Susunan pengurus akan ditampilkan setelah dikonfirmasi.</x-empty-state>
            @else
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($members as $member)
                        <article class="profile-member">
                            @if (isset($memberPhotos[$member->id]))
                                <x-public-photo :photo="$memberPhotos[$member->id]" image-class="aspect-[4/3] w-full bg-[#eef1e8] object-contain" />
                            @endif
                            <div class="p-6">
                                <p class="text-sm font-semibold text-[#416656]">{{ $member->position }}</p>
                                <h3 class="mt-2 text-xl font-semibold leading-tight wrap-anywhere text-pine">{{ $member->name }}</h3>
                                @if (filled($member->division))
                                    <p class="mt-3 wrap-anywhere text-[#416656]">{{ $member->division }}</p>
                                @endif
                                @if (filled($member->bio) && $member->id !== $chair?->id)
                                    <x-content-paragraphs :text="$member->bio" class="mt-4" />
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
