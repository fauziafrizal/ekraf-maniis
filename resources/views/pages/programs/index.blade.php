@extends('layouts.public')

@section('content')
    <x-page-heading class="section-masthead section-masthead--programs" title="Program" :description="'Lihat sasaran dan perkembangan program ' .
        $site['short_name'] .
        ' untuk warga, pelaku usaha, sekolah, dan komunitas.'" />
    <section class="site-shell section-body" aria-label="Daftar program">
        @if ($programs->isEmpty())
            <div class="program-empty" data-reveal>
                <p>Informasi program akan ditambahkan setelah siap dipublikasikan.</p>
                <a class="editorial-link" href="{{ route('contact') }}">Kontak pengurus</a>
            </div>
        @else
            <div data-em-filter>
                <div class="em-filter-tools" data-em-controls hidden>
                    <label for="program-search">Cari program</label><input type="search" id="program-search" data-em-query
                        placeholder="Nama program atau sasaran">
                    <label for="program-status">Status</label><select id="program-status" data-em-status>
                        <option value="">Semua status</option>
                        @foreach (\App\Enums\ProgramStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <p data-em-count role="status"></p>
                </div>
                <div class="program-index">
                    @foreach ($programs as $program)
                        <div @class(['program-entry', 'program-entry--featured' => $loop->first]) data-reveal data-em-item="{{ $program->status->value }}">
                            <x-program-card :program="$program" />
                        </div>
                    @endforeach
                </div>
                <p data-em-empty class="em-service-note" hidden>Program tidak ditemukan.</p>
            </div>
        @endif
    </section>
@endsection
