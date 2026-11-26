@extends('layouts.public')
@section('content')
    <x-page-heading title="Ruang Saya" eyebrow="Perjalanan kreatif Anda"
        description="Lanjutkan belajar dan temukan kembali produk atau usaha yang Anda simpan. Tidak perlu akun." />
    <div class="site-shell em-service-layout" data-my-space>
        <section class="em-tool-card">
            <h2 class="section-title">Progres pembelajaran</h2>
            <div class="em-my-progress">
                @foreach ([['academy', 'Ekraf Academy', 6, 'creative.academy'], ['student', 'Ekraf Pelajar', 4, 'creative.students']] as [$key, $label, $total, $route])
                    <article data-my-course="{{ $key }}" data-total="{{ $total }}">
                        <h3>{{ $label }}</h3><progress value="0" max="{{ $total }}"></progress>
                        <p data-my-course-label>0 dari {{ $total }} selesai</p><a class="editorial-link"
                            href="{{ route($route) }}">Lanjutkan belajar</a>
                    </article>
                @endforeach
            </div>
        </section>
        <section class="em-tool-card">
            <h2 class="section-title">Favorit saya</h2>
            <p data-my-empty>Belum ada produk atau usaha yang disimpan. Gunakan tombol favorit di katalog.</p>
            <div class="em-hub-grid">
                @foreach ($listings as $item)
                    <article data-my-listing="{{ $item->id }}" class="em-service-card" hidden>
                        <p>{{ $item->category }}</p>
                        <h3>{{ $item->name }}</h3>
                        <p>{{ Str::limit($item->description, 160) }}</p>
                        <a class="editorial-link"
                            href="{{ route($item->kind === 'product' ? 'creative.market' : 'creative.directory') }}">Buka
                            katalog</a>
                        <button class="button-secondary" type="button" data-my-remove="{{ $item->id }}">Hapus dari
                            favorit</button>
                    </article>
                @endforeach
            </div>
            <a class="button-primary" href="{{ route('creative.market') }}">Jelajahi marketplace</a>
        </section>
        <section class="em-tool-card">
            <h2 class="section-title">Rencana pengembangan</h2>
            <p data-my-readiness>0 dari 8 langkah kesiapan usaha selesai.</p>
            <a class="editorial-link" href="{{ route('creative.toolkit') }}#kesiapan">Buka checklist usaha</a>
        </section>
        <p class="em-service-note">Progres dan favorit hanya disimpan di browser ini, bukan akun atau database pribadi. Data
            katalog yang tidak lagi dipublikasikan tidak ditampilkan.</p>
    </div>
@endsection
