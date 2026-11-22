@extends('layouts.public')

@section('content')
    <x-page-heading title="Program" :description="'Lihat sasaran dan perkembangan program '.$site['short_name'].' untuk warga, pelaku usaha, sekolah, dan komunitas.'" />

    <section class="site-shell py-12 sm:py-16 lg:py-20" aria-label="Daftar program">
        @if ($programs->isEmpty())
            <x-empty-state>Informasi program akan ditambahkan setelah siap dipublikasikan.</x-empty-state>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($programs as $program)
                    <x-program-card :program="$program" />
                @endforeach
            </div>
        @endif
    </section>
@endsection
