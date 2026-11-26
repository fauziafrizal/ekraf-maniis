<section class="em-experience-section" aria-labelledby="extra-tools-title">
    <div class="site-shell">
        <div class="em-section-lead" data-reveal>
            <div>
                <p class="em-kicker">Lebih dari sekadar informasi</p>
                <h2 id="extra-tools-title" class="section-title">Bekal untuk berkarya.</h2>
            </div>
            <p class="prose-copy">Gunakan alat bantu praktis, rencanakan kegiatan, dan lanjutkan perjalanan kreatif Anda.
            </p>
        </div>
        <div class="em-extra-grid">
            @foreach ([['Creative Business Toolkit', 'Rekomendasi pembinaan, kalkulator bisnis, caption, dan checklist usaha.', 'creative.toolkit'], ['Kalender Kreatif', 'Telusuri agenda bulanan dan simpan pengingat kalender.', 'creative.calendar'], ['Ruang Saya', 'Lanjutkan progres belajar dan temukan favorit Anda.', 'creative.space'], ['Layanan Digital & Pelacakan', 'Ajukan kebutuhan dan pantau riwayat status dengan kode rahasia.', 'desk.index']] as [$title, $description, $route])
                <a href="{{ route($route) }}"
                    data-reveal><strong>{{ $title }}</strong><span>{{ $description }}</span><span>Jelajahi
                        layanan</span></a>
            @endforeach
        </div>
    </div>
</section>
