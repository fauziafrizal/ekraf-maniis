@extends('layouts.public')

@section('content')
    <x-page-heading
        title="Subsektor"
        description="Ragam bidang ekonomi kreatif di Kecamatan Maniis."
    />

    <section class="site-shell py-12 sm:py-16 lg:py-20" aria-labelledby="daftar-subsektor">
        <figure class="mb-8 overflow-hidden rounded-2xl border border-pine/15 bg-white">
            <img src="{{ asset('images/subsektor.jpg') }}" alt="Potensi subsektor ekonomi kreatif Kecamatan Maniis" class="max-h-152 w-full bg-[#eef1e8] object-contain">
        </figure>
        <h2 id="daftar-subsektor" class="sr-only">Daftar subsektor</h2>

        @if (! $subsectors->isEmpty())
            <ul class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" role="list">
                @foreach ($subsectors as $subsector)
                    <li class="min-w-0 rounded-2xl border border-pine/15 bg-white/70 p-6 sm:p-7">
                        <h3 class="mb-3 text-xl font-bold tracking-tight text-pine wrap-anywhere">{{ $subsector->name }}</h3>
                        <p class="prose-copy wrap-anywhere">{{ $subsector->description }}</p>

                        @if (filled($subsector->examples))
                            <div class="mt-5 border-t border-pine/15 pt-4">
                                <p class="text-sm font-semibold text-pine">Contoh kegiatan atau karya</p>
                                <p class="prose-copy mt-1 whitespace-pre-line wrap-anywhere">{{ $subsector->examples }}</p>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
