<section class="em-experience-section" aria-labelledby="community-tools-title">
    <div class="site-shell">
        <div class="em-section-lead">
            <div>
                <p class="em-kicker">Komunitas & inovasi</p>
                <h2 class="section-title" id="community-tools-title">Berkarya bersama.</h2>
            </div>
            <p class="prose-copy">Temukan ruang berbagi, pembelajaran, kolaborasi, dan pelayanan digital.</p>
        </div>
        <div class="em-extra-grid">
            @foreach ([['Creative Community Forum', 'Berbagi gagasan dan karya melalui moderasi.', 'community.index', ['jenis' => 'forum']], ['Pameran & Portofolio', 'Jelajahi karya kreator lokal.', 'community.index', ['jenis' => 'exhibition']], ['Kompetisi & Creative Challenge', 'Informasi lomba, gagasan dan kiriman karya.', 'community.index', ['jenis' => 'competition']], ['Peluang Kolaborasi', 'Temukan usulan kerja sama kreatif.', 'community.index', ['jenis' => 'collaboration']], ['Peta UMKM & Tur Virtual', 'Jelajahi lokasi usaha yang dipublikasikan.', 'community.map', []], ['Kuis, Poin & Daily Tips', 'Belajar dan raih lencana pribadi.', 'community.learning', []], ['Notifikasi Kegiatan', 'Lihat agenda dan pengingat kegiatan.', 'community.alerts', []], ['AI Creative Studio', 'Buat draf ide usaha dan deskripsi produk.', 'community.studio', []], ['Survei Kepuasan', 'Sampaikan masukan untuk portal.', 'community.feedback', []]] as [$title, $desc, $route, $params])
                <a href="{{ route($route, $params) }}"
                    data-reveal><strong>{{ $title }}</strong><span>{{ $desc }}</span><span>Jelajahi</span></a>
            @endforeach
        </div>
    </div>
</section>
