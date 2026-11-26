@extends('layouts.public')
@section('content')
    <x-page-heading title="Panel Pengurus"
        description="Kelola direktori usaha dan katalog produk. Data baru tidak terbit tanpa konfirmasi izin." />
    <div class="site-shell em-service-layout">
        <a class="button-primary" href="{{ route('office.index') }}">Sekretariat Digital & Moderasi</a>
        <form method="post" action="{{ route('creative.logout') }}">@csrf <button class="button-primary">Keluar</button>
        </form>
        @if (session('status'))
            <p role="status" class="em-service-note">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div role="alert" class="em-service-note">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <section>
            <h2 class="section-title">Tambah data</h2>
            <form enctype="multipart/form-data" method="post" action="{{ route('creative.store') }}"
                class="em-admin-form em-service-card">
                @csrf @include('pages.hub.listing-fields', ['record' => null])
                <button class="button-primary">Simpan data</button>
            </form>
        </section>
        <section>
            <h2 class="section-title">Kelola publikasi</h2>
            @forelse($listings as $record)
                <details class="em-service-card em-admin-record">
                    <summary>{{ $record->name }} — {{ $record->published ? 'Publik' : 'Draf' }}</summary>
                    <form enctype="multipart/form-data" method="post" action="{{ route('creative.update', $record) }}"
                        class="em-admin-form">
                        @csrf @method('PATCH') @include('pages.hub.listing-fields', ['record' => $record])
                        <button class="button-primary">Perbarui data</button>
                    </form>
                </details>
            @empty <p>Belum ada data.</p>
            @endforelse
            {{ $listings->links() }}
        </section>
    </div>
@endsection
