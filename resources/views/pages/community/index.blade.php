@extends('layouts.public')
@section('content')
    <x-page-heading :title="$pageTitle" eyebrow="Creative Community"
        description="Ruang berbagi karya, gagasan dan kesempatan kolaborasi. Semua kiriman dimoderasi sebelum tampil." />
    <div class="site-shell em-service-layout">
        <nav class="em-tool-nav" aria-label="Ruang komunitas">
            @foreach ($types as $key => $title)
                <a href="{{ route('community.index', ['jenis' => $key]) }}"
                    @if ($kind === $key) aria-current="page" @endif>{{ $title }}</a>
            @endforeach
        </nav>
        @if (session('status'))
            <p role="status" class="em-service-note">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p role="alert">{{ $errors->first() }}</p>
        @endif
        @if ($kind === 'competition' || $kind === 'challenge')
            <p class="em-service-note">Ruang publikasi kegiatan dan kiriman karya. Ketentuan lomba, jadwal, dan penilaian
                mengikuti pengumuman resmi pengurus; pengiriman tidak otomatis menjadi pendaftaran lomba.</p>
        @endif
        <div class="em-hub-grid">
            @forelse($posts as $post)
                <article class="em-service-card">
                    @if ($post->photo_path)
                        <img draggable="false" class="em-community-photo" src="{{ route('community.photo', $post) }}" loading="lazy"
                            alt="Karya {{ $post->title }}">
                    @endif
                    <p class="em-kicker">{{ $post->display_name }}</p>
                    <h3>{{ $post->title }}</h3>
                    <p class="em-prewrap">{{ $post->body }}</p>
                    @if ($post->url)
                        <a class="editorial-link" href="{{ $post->url }}" rel="nofollow noopener noreferrer"
                            target="_blank">Tautan kiriman</a>
                    @endif
                </article>
            @empty<p class="em-service-note">Belum ada kiriman yang dipublikasikan.</p>
            @endforelse
        </div>{{ $posts->links() }}
        <section class="em-tool-card">
            <h2 class="section-title">Kirim karya atau gagasan</h2>
            <form method="post" action="{{ route('community.submit') }}" enctype="multipart/form-data"
                class="em-admin-form">
                @csrf<input type="hidden" name="kind" value="{{ $kind }}">
                <label for="post-title">Judul</label><input id="post-title" name="title" required maxlength="150"
                    value="{{ old('title') }}">
                <label for="post-name">Nama publik atau nama kreator</label><input id="post-name" name="display_name"
                    required maxlength="100" value="{{ old('display_name') }}">
                <label for="post-body">Cerita atau informasi</label>
                <textarea id="post-body" name="body" required maxlength="2000" rows="5">{{ old('body') }}</textarea>
                <label for="post-url">Tautan karya (opsional)</label><input id="post-url" name="url" type="url"
                    maxlength="500">
                <label for="post-photo">Foto karya (opsional, JPG/PNG/WebP, maks. 4 MB)</label><input id="post-photo"
                    name="photo" type="file" accept="image/jpeg,image/png,image/webp">
                <label><input type="checkbox" name="consent" value="1" required> Saya memiliki izin atas karya dan
                    setuju nama publik, teks, dan foto ditampilkan setelah moderasi. Jangan masukkan data pribadi
                    sensitif.</label>
                <button class="button-primary">Kirim untuk moderasi</button>
            </form>
        </section>
    </div>
@endsection
