@extends('layouts.public')

@section('content')
    <x-page-heading
        class="section-masthead section-masthead--programs"
        title="Program"
        :description="'Lihat sasaran dan perkembangan program '.$site['short_name'].' untuk warga, pelaku usaha, sekolah, dan komunitas.'"
    />

    <section class="site-shell section-body" aria-label="Daftar program">
        @if ($programs->isEmpty())
            <div class="program-empty" data-reveal>
                <p>Informasi program akan ditambahkan setelah siap dipublikasikan.</p>
                <a class="editorial-link" href="{{ route('contact') }}">Kontak pengurus</a>
            </div>
        @else
            <div class="program-index">
                @foreach ($programs as $program)
                    <div @class(['program-entry', 'program-entry--featured' => $loop->first]) data-reveal>
                        <x-program-card :program="$program" />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
