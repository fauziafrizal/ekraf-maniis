@extends('layouts.public')
@section('content')
    <x-page-heading title="Layanan Digital & Pelacakan" eyebrow="Pusat layanan terpadu" description="Ajukan kebutuhan pembinaan, pendataan, atau kerja sama. Pantau status menggunakan nomor pengajuan dan kode rahasia." />
    <div class="site-shell em-service-layout">
        @if(session('desk_receipt'))
            <section class="em-tool-card" role="status">
                <h2 class="section-title">Pengajuan diterima</h2>
                <p>Simpan kedua kode ini secara privat. Kode rahasia hanya ditampilkan pada konfirmasi ini dan diperlukan untuk melacak pengajuan.</p>
                <p>Nomor pengajuan: <strong class="em-prewrap">{{ session('desk_receipt.reference') }}</strong></p>
                <p>Kode rahasia: <strong class="em-prewrap">{{ session('desk_receipt.secret') }}</strong></p>
                <p>Pengajuan belum berarti persetujuan atau pemberian bantuan.</p>
            </section>
        @endif
        @if($errors->any())
            <div role="alert" class="em-service-note">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        @endif
        @isset($tracking)
            <section class="em-tool-card" aria-labelledby="tracking-title">
                <p class="em-kicker">{{ $tracking['reference'] }}</p>
                <h2 id="tracking-title" class="section-title">{{ $statuses[$tracking['status']] }}</h2>
                <p>{{ $categories[$tracking['category']] }}</p>
                <h3>Riwayat status</h3>
                <ol class="em-desk-timeline">
                    @foreach($timeline as $event)
                        <li><strong>{{ $statuses[$event->status] }}</strong><time datetime="{{ $event->created_at->toIso8601String() }}">{{ $event->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</time></li>
                    @endforeach
                </ol>
                <a href="{{ route('desk.index') }}" class="button-secondary">Lacak pengajuan lain</a>
            </section>
        @endisset
        <div class="em-desk-grid">
            <section class="em-tool-card">
                <p class="em-kicker">01 · Ajukan</p>
                <h2 class="section-title">Pengajuan baru</h2>
                <form method="post" action="{{ route('desk.submit') }}" class="em-admin-form">@csrf
                    <label>Kategori<select name="category" required>@foreach($categories as $key=>$label)<option value="{{ $key }}" @selected(old('category')===$key)>{{ $label }}</option>@endforeach</select></label>
                    <label>Nama pemohon<input name="applicant" maxlength="100" required autocomplete="name" value="{{ old('applicant') }}"></label>
                    <label>Kontak untuk tindak lanjut (opsional)<input name="contact" maxlength="150" value="{{ old('contact') }}" placeholder="Email atau WhatsApp"></label>
                    <label>Kebutuhan atau usulan<textarea name="description" minlength="10" maxlength="2000" rows="5" required>{{ old('description') }}</textarea></label>
                    <p class="em-service-note">Data hanya dapat dibaca pengurus yang berwenang. Jangan masukkan NIK, kata sandi, data keuangan, atau dokumen sensitif. Kontak hanya untuk tindak lanjut pengajuan.</p>
                    <label><input type="checkbox" name="consent" value="1" required> Saya menyetujui penyimpanan data untuk memproses pengajuan ini.</label>
                    <button class="button-primary">Simpan pengajuan</button>
                </form>
            </section>
            <section class="em-tool-card">
                <p class="em-kicker">02 · Pantau</p>
                <h2 class="section-title">Lacak status</h2>
                <p>Gunakan kode yang diterima saat mengajukan layanan. Jangan bagikan kode rahasia kepada pihak lain.</p>
                <form method="post" action="{{ route('desk.track') }}" class="em-admin-form">@csrf
                    <label>Nomor pengajuan<input name="reference" required maxlength="19" autocomplete="off" placeholder="EK-..."></label>
                    <label>Kode rahasia<input type="password" name="password" required minlength="32" maxlength="32" autocomplete="off"></label>
                    <button class="button-primary">Periksa status</button>
                </form>
                <p class="em-service-note">Status ditampilkan tanpa nama, kontak, isi pengajuan, dan catatan internal. Jika kode hilang, hubungi pengurus.</p>
            </section>
        </div>
    </div>
@endsection
