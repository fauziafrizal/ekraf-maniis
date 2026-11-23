@extends('layouts.public')

@section('content')
    <x-page-heading :title="'Profil '.$site['short_name']" eyebrow="Tentang kami" :description="$site['name']">
        <a class="button-primary" href="#pengurus">Lihat Struktur Pengurus</a>
    </x-page-heading>

    <section class="profile-intro" aria-labelledby="about-title">
        <div class="site-shell profile-intro-grid">
            <div>
                <h2 id="about-title" class="section-title">Tentang {{ $site['short_name'] }}</h2>
                <div class="prose-copy mt-5 space-y-4">
                    @foreach ($site['profile'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>

            <aside class="profile-period" aria-label="Masa kepengurusan">
                <p class="eyebrow">Masa kepengurusan</p>
                <p class="profile-period-years">{{ $site['period'] }}</p>
                <p class="profile-slogan">{{ $site['slogan'] }}</p>
            </aside>
        </div>
    </section>

    <section id="pengurus" class="leadership-section" data-leadership aria-labelledby="board-title">
        <div class="site-shell">
            <header class="leadership-heading">
                <h2 id="board-title" class="section-title">Struktur Pengurus</h2>
                @unless ($hasPublishedLeaders)
                    <p class="leadership-note">
                        {{ $otherMembers->isEmpty() ? 'Susunan pengurus akan ditampilkan setelah dikonfirmasi.' : 'Profil pimpinan akan ditampilkan setelah dikonfirmasi.' }}
                    </p>
                @endunless
            </header>

            <div class="leadership-grid">
                @foreach ($leadership as $role => $leader)
                    @php($member = $leader['member'])
                    <x-leadership-card
                        :role="$role"
                        :position="$leader['position']"
                        :member="$member"
                        :photo="$member ? $memberPhotos->get($member->id) : null"
                        :show-pending-note="$hasPublishedLeaders"
                    />
                @endforeach
            </div>

            @if ($otherMembers->isNotEmpty())
                <section class="leadership-other" aria-labelledby="other-members-title">
                    <h3 id="other-members-title" class="section-title">Pengurus lainnya</h3>
                    <div class="leadership-other-grid">
                        @foreach ($otherMembers as $member)
                            <article class="leadership-member">
                                @if ($photo = $memberPhotos->get($member->id))
                                    <x-public-photo :photo="$photo" class="leadership-photo" image-class="leadership-portrait" />
                                @endif
                                <div class="leadership-content">
                                    <p class="leadership-position">{{ $member->position }}</p>
                                    <h4 class="leadership-name">{{ $member->name }}</h4>
                                    @if (filled($member->division))
                                        <p class="leadership-division">{{ $member->division }}</p>
                                    @endif
                                    @if (filled($member->bio))
                                        <x-content-paragraphs :text="$member->bio" class="leadership-bio" />
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection
