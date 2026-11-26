@extends('layouts.public')
@section('content')
    <x-page-heading title="Sekretariat Digital"
        description="Administrasi dan moderasi internal pengurus. Data privat tidak ditampilkan pada halaman publik." />
    <div class="site-shell em-service-layout">
        <a class="button-secondary" href="{{ route('creative.admin') }}">Kelola katalog</a>
        <a class="button-primary" href="{{ route('desk.queue') }}">Antrean pengajuan layanan</a>
        @if (session('status'))
            <p role="status" class="em-service-note">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <nav class="em-tool-nav">
            @foreach (['dokumen' => 'E-Surat / Arsip / Notulen', 'kartu' => 'Sertifikat & Anggota', 'moderasi' => 'Moderasi', 'voting' => 'E-Voting', 'survei' => 'Survei'] as $id => $label)
                <a href="#{{ $id }}">{{ $label }}</a>
            @endforeach
        </nav>
        <section class="em-tool-card" id="dokumen">
            <h2 class="section-title">E-Surat, E-Arsip & E-Notulen</h2>
            <form method="post" action="{{ route('office.document') }}" enctype="multipart/form-data"
                class="em-admin-form">@csrf
                <label>Jenis dokumen<select name="kind">
                        @foreach (['surat_masuk' => 'Surat masuk', 'surat_keluar' => 'Surat keluar', 'arsip' => 'Arsip', 'notulen' => 'Notulen'] as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Nomor<input name="number" maxlength="100" required></label><label>Judul<input name="title"
                        maxlength="200" required></label><label>Tanggal<input name="document_date" type="date"
                        required></label>
                <label>Isi surat, ringkasan arsip atau notulen
                    <textarea name="body" maxlength="15000" rows="6" required></textarea>
                </label><label>Lampiran PDF (opsional)<input type="file" name="file"
                        accept="application/pdf"></label><button class="button-primary">Simpan dokumen privat</button>
            </form>
            @foreach ($documents as $document)
                <details class="em-calendar-event">
                    <summary>{{ $document->number }} · {{ $document->title }}</summary>
                    <p>{{ $document->kind }} · {{ $document->document_date }}</p>
                    <p class="em-prewrap">{{ $document->body }}</p><a class="editorial-link"
                        href="{{ route('office.print', $document) }}">Buka versi cetak</a>
                    @if ($document->file_path)
                        <a class="editorial-link" href="{{ route('office.download', $document) }}">Unduh lampiran</a>
                    @endif
                </details>
            @endforeach
            {{ $documents->links() }}
        </section>
        <section class="em-tool-card" id="kartu">
            <h2 class="section-title">E-Sertifikat & Kartu Anggota</h2>
            <form method="post" action="{{ route('office.issue') }}" class="em-admin-form">@csrf
                <label>Jenis<select name="kind">
                        <option value="certificate">Sertifikat</option>
                        <option value="member">Kartu anggota</option>
                    </select></label><label>Nomor unik<input name="number" required maxlength="100"></label><label>Nama
                    penerima yang diizinkan<input name="recipient" required maxlength="150"></label><label>Judul /
                    peran<input name="title" required maxlength="200"></label><label>Tanggal terbit<input name="issued_on"
                        type="date" required></label><label><input type="checkbox" name="consent" value="1"
                        required> Saya memiliki kewenangan penerbitan dan izin penerima untuk verifikasi melalui tautan
                    unik.</label><button class="button-primary">Terbitkan dokumen QR</button>
            </form>
            @foreach ($credentials as $credential)
                <div class="em-calendar-event"><strong>{{ $credential->number }} · {{ $credential->recipient }}</strong>
                    <p>{{ $credential->active ? 'Aktif' : 'Dicabut' }}</p>
                    @if ($credential->active)
                        <a class="editorial-link" href="{{ route('community.verify', $credential->token) }}">Buka kartu /
                            sertifikat</a>
                        <form method="post" action="{{ route('office.revoke', $credential) }}">@csrf<button
                                class="button-secondary">Cabut dokumen</button></form>
                    @endif
                </div>
            @endforeach{{ $credentials->links() }}
        </section>
        <section class="em-tool-card" id="moderasi">
            <h2 class="section-title">Moderasi komunitas dan ulasan</h2>
            @foreach ([['post', $posts], ['review', $reviews]] as [$kind, $records])
                <h3>{{ $kind === 'post' ? 'Kiriman komunitas' : 'Ulasan produk' }}</h3>
                @forelse($records as $record)
                    <article class="em-calendar-event"><strong>{{ $record->title ?? 'Ulasan ' . $record->rating . '/5' }} ·
                            {{ $record->display_name }}</strong>
                        <p class="em-prewrap">{{ $record->body }}</p>
                        @if ($kind === 'post' && $record->photo_path)
                            <a href="{{ route('office.post-preview', $record) }}">Pratinjau foto privat</a>
                            @endif @if ($kind === 'post' && $record->url)
                                <p>{{ $record->url }}</p>
                            @endif
                            <p>
                                {{ $record->published ? 'Publik' : 'Draf' }}</p>
                            <form method="post" action="{{ route('office.moderate', [$kind, $record->id]) }}">@csrf<input
                                    type="hidden" name="published" value="{{ $record->published ? 0 : 1 }}"><button
                                    class="button-secondary">{{ $record->published ? 'Tarik publikasi' : 'Terbitkan setelah diperiksa' }}</button>
                            </form>
                </article>@empty<p>Belum ada kiriman.</p>
                @endforelse{{ $records->links() }}
            @endforeach
        </section>
        <section class="em-tool-card" id="voting">
            <h2 class="section-title">E-Voting internal</h2>
            <p>Voting terbatas pada akun pengurus, satu suara per akun. Ini bukan sistem pemilu atau voting anonim.</p>
            <form method="post" action="{{ route('office.poll') }}" class="em-admin-form">@csrf<label>Judul voting<input
                        name="title" maxlength="200" required></label><label>Pilihan, satu per baris
                    <textarea name="options" rows="4" required maxlength="1000"></textarea>
                </label><button class="button-primary">Buka voting</button></form>
            @foreach ($polls as $poll)
                <article class="em-calendar-event">
                    <h3>{{ $poll->title }}</h3>
                    <p>{{ $poll->open ? 'Terbuka' : 'Ditutup' }}</p>
                    @php($counts = \App\Models\EkrafVote::where('ekraf_poll_id', $poll->id)->selectRaw('choice, count(*) as total')->groupBy('choice')->pluck('total', 'choice'))
                    @foreach ($poll->options as $index => $option)
                        <p>{{ $option }} — {{ $counts[$index] ?? 0 }} suara</p>
                    @endforeach
                    @if ($poll->open)
                        <form method="post" action="{{ route('office.vote', $poll) }}" class="em-admin-form">
                            @csrf<label>Pilihan<select name="choice">
                                    @foreach ($poll->options as $index => $option)
                                        <option value="{{ $index }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </label><button class="button-primary">Pilih</button></form>
                        <form method="post" action="{{ route('office.close', $poll) }}">@csrf<button
                                class="button-secondary">Tutup voting</button></form>
                    @endif
                </article>
            @endforeach{{ $polls->links() }}
        </section>
        <section class="em-tool-card" id="survei">
            <h2 class="section-title">Masukan pengunjung</h2>
            @forelse($feedback as $item)
                <article class="em-calendar-event"><strong>{{ $item->rating }}/5</strong>
                    <p>{{ $item->body }}</p>
            </article>@empty<p>Belum ada masukan.</p>
            @endforelse{{ $feedback->links() }}
        </section>
</div>@endsection
