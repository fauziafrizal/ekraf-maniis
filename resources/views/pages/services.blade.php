@extends('layouts.public')
@section('content')
    <x-page-heading title="Pusat Layanan Ekraf" eyebrow="Ekraf Maniis"
        description="Temukan informasi, akses layanan, dan jadwal program ekonomi kreatif dalam satu tempat." />
    <div class="site-shell em-service-layout">
        <a class="button-primary" href="{{ route('desk.index') }}">Pengajuan digital & pelacakan status</a>
        <nav class="em-section-shortcuts" aria-label="Bagian pusat layanan">
            <a href="#layanan-title">Pengajuan layanan</a>
            <a href="#agenda">Agenda</a>
            <a href="#pencarian">Pencarian</a>
            <a href="#faq">Pertanyaan umum</a>
        </nav>
        <section aria-labelledby="layanan-title">
            <h2 class="section-title" id="layanan-title">Apa yang Anda butuhkan?</h2>
            <div class="em-services-grid">
                @foreach ([['Pendataan pelaku ekraf', 'Sampaikan informasi usaha atau karya Anda untuk pendataan awal.', 'Pendaftaran UMKM/Pelaku Ekraf'], ['Pengajuan pembinaan', 'Sampaikan kebutuhan pelatihan, pendampingan, atau pengembangan usaha.', 'Permohonan pelatihan'], ['Kemitraan dan kolaborasi', 'Ajukan kerja sama untuk komunitas, sekolah, atau mitra usaha.', 'Kerja sama atau kolaborasi'], ['Usulan program', 'Bagikan ide kegiatan dan inovasi untuk Maniis.', 'Usulan program'], ['Bergabung dengan Ekraf', 'Tanyakan proses bergabung dan keterlibatan dalam kegiatan.', 'Bergabung dengan Ekraf'], ['Informasi umum', 'Tanyakan informasi yang belum tersedia di website.', 'Informasi umum']] as [$title, $description, $category])
                    <article class="em-service-card em-service-choice">
                        <div class="em-service-card-top">
                            <span class="em-service-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="{{ ['M5 4h14v16H5zM9 8h6M9 12h6M9 16h4','m3 8 9-5 9 5-9 5-9-5Zm4 3v6l5 3 5-3v-6','M8 12l3 3 5-6M4 5h16v14H4z','M12 3a7 7 0 0 0-4 13v2h8v-2a7 7 0 0 0-4-13ZM9 21h6','M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6m-3-3h6','M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20ZM9 9a3 3 0 1 1 4 3v2M13 17h.01'][$loop->index] }}" />
                                </svg>
                            </span>
                            <span class="em-service-number" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3>{{ $title }}</h3>
                        <p>{{ $description }}</p>
                        <a class="editorial-link"
                            href="{{ route('contact', ['kategori' => $category]) }}#form-kolaborasi">Ajukan melalui
                            WhatsApp</a>
                    </article>
                @endforeach
            </div>
            <p class="em-service-note">Pengajuan disusun menjadi pesan WhatsApp untuk Anda kirim sendiri. Pengajuan bukan
                persetujuan otomatis, dan data formulir tidak disimpan oleh website.</p>
        </section>
        <section id="agenda" aria-labelledby="agenda-title">
            <h2 class="section-title" id="agenda-title">Agenda mendatang</h2>
            <p class="prose-copy">Jadwal program yang sudah dipublikasikan dan memiliki tanggal pelaksanaan.</p>
            @forelse ($agenda as $program)
                <article class="em-agenda-item">
                    <div class="em-agenda-date">
                        <strong>{{ $program->starts_at->format('d') }}</strong>
                        <span>{{ $program->starts_at->translatedFormat('M Y') }}</span>
                    </div>
                    <div>
                        <x-program-status :status="$program->status" />
                        <h3><a href="{{ route('programs.show', $program->slug) }}">{{ $program->title }}</a></h3>
                        <p>{{ $program->starts_at->translatedFormat('j F Y, H.i') }} WIB @if ($program->location)
                                · {{ $program->location }}
                            @endif
                        </p>
                        @if ($program->registrationUrl())
                            <a class="editorial-link" href="{{ $program->registrationUrl() }}" target="_blank"
                                rel="noopener noreferrer">Informasi pendaftaran</a>
                        @endif
                    </div>
                </article>
            @empty
                <p class="em-service-note">Belum ada agenda mendatang yang dipublikasikan. Silakan pantau halaman program.
                </p>
            @endforelse
            <a class="editorial-link" href="{{ route('programs.index') }}">Jelajahi program</a>
            <a class="button-secondary" href="{{ route('creative.calendar') }}">Buka kalender kreatif</a>
        </section>
        <section id="pencarian" aria-labelledby="search-title">
            <h2 class="section-title" id="search-title">Cari informasi</h2>
            <div data-em-filter>
                <div class="em-filter-tools" hidden data-em-controls>
                    <label for="site-search">Kata kunci</label>
                    <input id="site-search" type="search" data-em-query placeholder="Contoh: pelatihan, kuliner, Cirata">
                    <p data-em-count role="status"></p>
                </div>
                <div class="em-search-list">
                    @foreach ($searchItems as $item)
                        <article class="em-search-item" data-em-item>
                            <span>{{ $item['type'] }}</span>
                            <h3><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></h3>
                            <p>{{ $item['description'] }}</p>
                        </article>
                    @endforeach
                </div>
                <p class="em-service-note" data-em-empty hidden>Informasi tidak ditemukan. Coba kata kunci lain.</p>
            </div>
        </section>
        <section id="faq" aria-labelledby="faq-title">
            <h2 class="section-title" id="faq-title">Pertanyaan yang sering diajukan</h2>
            <div class="em-faq">
                @foreach ([['Apa itu Ekraf Maniis?', 'Ruang koordinasi warga, pelaku usaha, sekolah, komunitas, dan calon mitra untuk mengembangkan ekonomi kreatif di Kecamatan Maniis.'], ['Bagaimana cara mengajukan pendataan usaha?', 'Pilih layanan Pendataan pelaku ekraf, isi informasi yang diperlukan, lalu kirim pesan WhatsApp kepada pengurus.'], ['Bagaimana mengetahui jadwal kegiatan?', 'Lihat agenda mendatang dan halaman Program. Jadwal akan tampil ketika telah dipublikasikan.'], ['Apakah pengajuan menjamin mendapatkan bantuan?', 'Tidak. Pengurus perlu meninjau pengajuan dan mengonfirmasi ketersediaan program atau pembinaan.'], ['Apakah sekolah dan komunitas dapat berkolaborasi?', 'Ya, ide kolaborasi dapat disampaikan melalui formulir pengajuan.'], ['Bagaimana data formulir digunakan?', 'Website menyusun pesan untuk dikirim melalui WhatsApp. Jangan mencantumkan NIK, kata sandi, atau data sensitif yang tidak diperlukan.']] as [$question, $answer])
                    <details>
                        <summary>{{ $question }}</summary>
                        <p>{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
        </section>
    </div>
@endsection
