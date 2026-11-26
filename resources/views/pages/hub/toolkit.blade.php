@extends('layouts.public')
@section('content')
    <x-page-heading title="Creative Business Toolkit" eyebrow="Alat bantu usaha"
        description="Alat praktis untuk merencanakan usaha, promosi, dan pengembangan keterampilan. Perhitungan diproses di browser." />
    <div class="site-shell em-service-layout em-toolkit">
        <noscript>
            <p class="em-service-note">Aktifkan JavaScript untuk menggunakan alat perhitungan interaktif.</p>
        </noscript>
        <section aria-labelledby="toolkit-title">
            <h2 id="toolkit-title" class="section-title">Pilih alat yang Anda butuhkan</h2>
            <div class="em-tool-nav">
                @foreach ([['rekomendasi', 'Rekomendasi pembinaan'], ['impas', 'Titik impas'], ['laba', 'Simulasi laba'], ['promosi', 'Draf promosi'], ['kesiapan', 'Checklist usaha']] as [$id, $label])
                    <a href="#{{ $id }}">{{ $label }}</a>
                @endforeach
            </div>
        </section>
        <section id="rekomendasi" class="em-tool-card">
            <p class="em-kicker">01 · Rekomendasi berbasis aturan</p>
            <h2 class="section-title">Temukan kebutuhan pembinaan</h2>
            <p>Jawab pertanyaan sederhana untuk memperoleh saran langkah awal. Ini bukan hasil klasifikasi Random Forest
                atau keputusan resmi pengurus.</p>
            <form hidden data-tool-recommend class="em-tool-form">
                <label for="rec-stage">Tahap usaha</label>
                <select id="rec-stage" name="stage">
                    <option value="idea">Baru memiliki ide</option>
                    <option value="start">Sudah mulai menjual</option>
                    <option value="grow">Ingin mengembangkan usaha</option>
                </select>
                <fieldset>
                    <legend>Kendala yang dihadapi (boleh lebih dari satu)</legend>
                    @foreach ([['marketing', 'Pemasaran dan pelanggan'], ['brand', 'Kemasan dan merek'], ['finance', 'Pencatatan biaya dan harga'], ['legal', 'Informasi legalitas'], ['skills', 'Keterampilan produksi']] as [$key, $label])
                        <label class="em-tool-check"><input type="checkbox" name="needs" value="{{ $key }}">
                            {{ $label }}</label>
                    @endforeach
                </fieldset>
                <button class="button-primary" type="submit">Lihat rekomendasi</button>
                <div class="em-tool-result" data-tool-output role="status" hidden></div>
            </form>
            <a class="editorial-link"
                href="{{ route('contact', ['kategori' => 'Permohonan pelatihan']) }}#form-kolaborasi">Ajukan kebutuhan
                pembinaan</a>
        </section>
        <section id="impas" class="em-tool-card">
            <p class="em-kicker">02 · Kalkulator bisnis</p>
            <h2 class="section-title">Kalkulator titik impas</h2>
            <p>Perkirakan jumlah unit minimum yang perlu terjual untuk menutup biaya tetap.</p>
            <form hidden data-tool-bep class="em-tool-form">
                @foreach ([['fixed', 'Biaya tetap per periode (Rp)', 500000], ['variable', 'Biaya variabel per unit (Rp)', 10000], ['price', 'Harga jual per unit (Rp)', 15000]] as [$key, $label, $value])
                    <label for="bep-{{ $key }}">{{ $label }}</label><input id="bep-{{ $key }}"
                        name="{{ $key }}" type="number" min="0" max="100000000000" step="1"
                        value="{{ $value }}" required>
                @endforeach
                <button class="button-primary" type="submit">Hitung titik impas</button>
                <output data-tool-output class="em-tool-result" hidden></output>
            </form>
            <p class="em-service-note">Asumsi harga dan biaya variabel per unit tetap. Belum memperhitungkan pajak atau
                biaya yang tidak dimasukkan.</p>
        </section>
        <section id="laba" class="em-tool-card">
            <p class="em-kicker">03 · Simulasi keuangan</p>
            <h2 class="section-title">Simulasi pendapatan dan laba</h2>
            <form hidden data-tool-profit class="em-tool-form">
                @foreach ([['quantity', 'Jumlah unit terjual', 100], ['price', 'Harga jual per unit (Rp)', 15000], ['variable', 'Biaya variabel per unit (Rp)', 10000], ['fixed', 'Biaya tetap (Rp)', 200000]] as [$key, $label, $value])
                    <label for="profit-{{ $key }}">{{ $label }}</label><input
                        id="profit-{{ $key }}" name="{{ $key }}" type="number" min="0"
                        max="{{ $key === 'quantity' ? 1000000 : 1000000000 }}" step="1" value="{{ $value }}"
                        required>
                @endforeach
                <button class="button-primary" type="submit">Hitung simulasi</button>
                <output data-tool-output class="em-tool-result" hidden></output>
            </form>
            <p class="em-service-note">Estimasi laba berdasarkan angka yang dimasukkan, bukan prediksi pendapatan.</p>
        </section>
        <section id="promosi" class="em-tool-card">
            <p class="em-kicker">04 · Studio promosi</p>
            <h2 class="section-title">Pembuat draf caption produk</h2>
            <p>Susun draf berbasis template, tanpa mengarang manfaat atau testimoni.</p>
            <form hidden data-tool-caption class="em-tool-form">
                <label for="caption-name">Nama produk</label><input id="caption-name" name="product" maxlength="100"
                    required placeholder="Contoh: Keripik nangka">
                <label for="caption-benefit">Keunggulan yang benar</label>
                <textarea id="caption-benefit" name="benefit" maxlength="300" rows="3" required
                    placeholder="Jelaskan keunggulan produk Anda"></textarea>
                <label for="caption-cta">Ajakan</label><select id="caption-cta" name="cta">
                    <option>Hubungi kami untuk informasi pemesanan.</option>
                    <option>Tanyakan ketersediaan produk kepada kami.</option>
                    <option>Kenali produk lokal dan dukung karya kreatif.</option>
                </select>
                <button type="submit" class="button-primary">Buat draf caption</button>
                <textarea data-tool-caption-output rows="6" aria-label="Hasil draf caption" readonly hidden></textarea>
                <button type="button" data-tool-copy class="button-secondary" hidden>Salin caption</button>
                <p data-tool-copy-status role="status"></p>
            </form>
        </section>
        <section id="kesiapan" class="em-tool-card" data-tool-checklist>
            <p class="em-kicker">05 · Rencana usaha</p>
            <h2 class="section-title">Checklist kesiapan usaha</h2>
            <p>Catat persiapan secara pribadi. Checklist ini bukan sertifikasi atau penilaian kelayakan resmi.</p>
            <div class="em-tool-checklist">
                @foreach (['Menentukan calon pelanggan', 'Memiliki contoh produk', 'Menghitung biaya dan harga jual', 'Menyiapkan identitas dan foto produk', 'Menyiapkan kanal pemesanan', 'Memeriksa kebutuhan legalitas usaha', 'Mencatat penjualan dan pengeluaran', 'Mengumpulkan masukan pelanggan'] as $task)
                    <label class="em-tool-check"><input type="checkbox" data-tool-task="{{ $loop->index }}">
                        {{ $task }}</label>
                @endforeach
            </div>
            <progress data-tool-progress max="8" value="0" aria-label="Progres kesiapan usaha"></progress>
            <p data-tool-progress-label role="status">0 dari 8 langkah selesai</p>
            <button type="button" class="button-secondary" data-tool-reset>Reset checklist</button>
            <p class="em-service-note">Pilihan disimpan di browser ini jika penyimpanan lokal tersedia.</p>
        </section>
    </div>
@endsection
