@extends('layouts.public')

@section('content')
    <x-page-heading :eyebrow="$site['location']" title="Potensi Maniis" :description="$site['potential_intro']" />

    <section class="site-shell py-12 sm:py-16 lg:py-20" aria-labelledby="potensi-wilayah">
        <h2 id="potensi-wilayah" class="sr-only">Potensi wilayah Maniis</h2>

        <div class="border-b border-pine/20">
            @foreach ($potentials as $potential)
                <article id="{{ Str::slug($potential['title']) }}" @class([
                    'scroll-mt-8 border-t border-pine/20 py-8 sm:py-10',
                    'grid items-start gap-6 lg:grid-cols-2 lg:gap-12' => $potential['photo'],
                ])>
                    <div @class([
                        'grid items-baseline gap-4',
                        'md:grid-cols-[minmax(0,1fr)_minmax(0,1.6fr)] md:gap-10' => !$potential[
                            'photo'
                        ],
                    ])>
                        <h3 class="section-title">{{ $potential['title'] }}</h3>
                        <div class="prose-copy max-w-2xl space-y-4">
                            @foreach (Arr::wrap($potential['body'] ?? $potential['summary']) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>

                    @if ($potential['photo'])
                        <x-public-photo :photo="$potential['photo']" />
                    @endif
                </article>
            @endforeach
        </div>

        <a class="button-secondary mt-8 inline-flex" href="{{ route('subsectors') }}">Lihat Subsektor</a>
    </section>
@endsection
