@php
    $destinations = [
        ['home', 'Beranda', 'Portal utama', 'awal selamat datang'],
        ['profile', 'Profil organisasi', 'Kenali pengurus Ekraf', 'ketua wakil sekretaris struktur'],
        ['maniis', 'Potensi Maniis', 'Wisata, alam, dan potensi lokal', 'cirata curug keramba desa'],
        ['subsectors', 'Subsektor kreatif', 'Bidang ekonomi kreatif', 'kuliner kriya fashion musik'],
        ['programs.index', 'Program kerja', 'Informasi program pembinaan', 'program pelatihan'],
        ['activities.index', 'Kegiatan dan galeri', 'Berita serta dokumentasi', 'foto berita'],
        ['services', 'Pusat layanan', 'Agenda, pencarian, dan pertanyaan umum', 'layanan faq'],
        ['desk.index', 'Layanan digital', 'Ajukan layanan dan lacak pengajuan', 'tiket status pendataan pengaduan'],
        ['creative.hub', 'Creative Hub', 'Semua ruang kreatif', 'fitur portal'],
        ['creative.market', 'Marketplace', 'Temukan produk lokal', 'belanja katalog barang'],
        ['creative.directory', 'Direktori usaha', 'Temukan pelaku usaha', 'umkm bisnis toko'],
        ['creative.academy', 'Ekraf Academy', 'Materi pembelajaran usaha', 'belajar kelas'],
        ['creative.students', 'Ekraf Pelajar', 'Ruang kreativitas pelajar', 'sekolah siswa'],
        ['creative.toolkit', 'Alat usaha', 'Kalkulator dan panduan bisnis', 'laba bep harga modal'],
        ['creative.space', 'Ruang Saya', 'Favorit dan progres belajar', 'bookmark pribadi'],
        ['creative.calendar', 'Kalender kreatif', 'Jadwal kegiatan', 'agenda acara tanggal'],
        ['creative.data', 'Pusat data', 'Statistik ekonomi kreatif', 'data grafik'],
        ['creative.map', 'Peta potensi', 'Jelajahi wilayah Maniis', 'lokasi wisata'],
        ['community.map', 'Peta UMKM', 'Lokasi pelaku usaha terdaftar', 'lokasi usaha'],
        ['community.index', 'Komunitas', 'Karya dan kolaborasi kreator', 'forum portofolio'],
        ['community.studio', 'Studio kreatif', 'Bantu menyusun ide dan deskripsi', 'ai caption konten'],
        ['community.learning', 'Creative Challenge', 'Kuis dan tantangan belajar', 'kuis poin'],
        ['community.alerts', 'Notifikasi', 'Informasi pengingat kegiatan', 'pengingat'],
        ['community.feedback', 'Survei pengunjung', 'Sampaikan masukan', 'kritik saran'],
        ['creative.help', 'Pusat bantuan', 'Panduan dan privasi', 'bantuan keamanan'],
        ['contact', 'Kontak pengurus', 'WhatsApp dan kolaborasi', 'hubungi nomor telepon'],
    ];
@endphp
<dialog class="em-visitor-dialog" data-visitor-dialog aria-labelledby="visitor-dialog-title"
    data-current-route="{{ request()->route()?->getName() }}">
    <div class="em-visitor-head">
        <div><p class="em-visitor-eyebrow">Navigasi Ekraf Maniis</p><h2 id="visitor-dialog-title">Temukan yang Anda butuhkan.</h2></div>
        <button type="button" data-visitor-close class="em-visitor-close" aria-label="Tutup pencarian">×</button>
    </div>
    <div class="em-visitor-search-row">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
        <input type="search" data-visitor-query aria-label="Cari menu atau layanan" placeholder="Cari layanan, UMKM, pelatihan..." maxlength="100" autocomplete="off">
    </div>
    <nav class="em-visitor-tabs" aria-label="Kategori navigasi">
        <button type="button" data-visitor-tab="all" aria-pressed="true">Semua</button>
        <button type="button" data-visitor-tab="saved" aria-pressed="false">Disimpan</button>
        <button type="button" data-visitor-tab="recent" aria-pressed="false">Terakhir dibuka</button>
    </nav>
    <p class="em-visitor-status" data-visitor-status role="status"></p>
    <div class="em-visitor-results" data-visitor-results>
        @foreach ($destinations as [$name,$label,$description,$keywords])
            <div class="em-visitor-result" data-visitor-item="{{ $name }}" data-keywords="{{ $keywords }}">
                <a href="{{ route($name, absolute: false) }}"><strong>{{ $label }}</strong><span>{{ $description }}</span></a>
                <button type="button" class="em-visitor-save" data-visitor-save aria-label="Simpan {{ $label }}" aria-pressed="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M6 4h12v17l-6-4-6 4z"/></svg>
                </button>
            </div>
        @endforeach
    </div>
    <div class="em-visitor-foot">
        <a href="{{ route('services', absolute: false) }}#pencarian">Cari isi program, berita, dan subsektor</a>
        <button type="button" data-visitor-clear>Hapus riwayat</button>
        <p>Menu tersimpan dan riwayat hanya disimpan di browser ini.</p>
    </div>
</dialog>
