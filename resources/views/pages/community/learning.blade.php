@extends('layouts.public')
@section('content')
    <x-page-heading title="Creative Learning Challenge"
        description="Uji pengetahuan, dapatkan poin latihan, dan bangun kebiasaan berkarya." />
    <div class="site-shell em-service-layout">
        <section class="em-tool-card">
            <p class="em-kicker">Creative Daily Tips</p>
            <h2 class="section-title">Tips hari ini</h2>
            <p data-daily-tip>Catat biaya bahan dan kemasan sebelum menentukan harga jual.</p><button type="button"
                class="button-secondary" data-next-tip hidden>Tips lainnya</button>
        </section>
        <section class="em-tool-card" data-creative-quiz>
            <h2 class="section-title">Kuis ekonomi kreatif</h2>
            <form data-quiz-form hidden>
                @foreach ([['Apa langkah awal sebelum membuat produk?', ['Memvalidasi kebutuhan calon pelanggan', 'Membeli iklan mahal', 'Menyalin merek lain']], ['Biaya tetap Rp100.000 dan kontribusi Rp5.000 per produk. Berapa unit titik impas?', ['5 unit', '20 unit', '100 unit']], ['Foto produk yang baik sebaiknya…', ['Mengubah produk hingga tidak sesuai kenyataan', 'Menggunakan foto orang lain tanpa izin', 'Menampilkan produk secara jelas dan jujur']], ['Bagaimana menyampaikan klaim produk?', ['Berdasarkan fakta yang dapat dipertanggungjawabkan', 'Dengan testimoni fiktif', 'Dengan janji tanpa bukti']], ['Apa manfaat mencatat transaksi?', ['Menghindari semua risiko usaha', 'Mengetahui pemasukan, biaya dan laba', 'Menjamin penjualan naik']]] as [$question, $answers])
                    @php($questionIndex = $loop->index)
                    <fieldset class="em-tool-card">
                        <legend>{{ $loop->iteration }}. {{ $question }}</legend>
                        @foreach ($answers as $answer)
                            <label class="em-tool-check"><input required type="radio" name="q{{ $questionIndex }}"
                                    value="{{ $loop->index }}"> {{ $answer }}</label>
                        @endforeach
                    </fieldset>
                @endforeach
                <button class="button-primary">Periksa jawaban</button>
                <p data-quiz-result role="status"></p>
            </form>
            <noscript>Aktifkan JavaScript untuk mengikuti kuis.</noscript>
        </section>
        <section class="em-tool-card">
            <h2 class="section-title">Poin dan lencana pencapaian</h2>
            <p data-quiz-points>0 poin</p>
            <p data-quiz-badge>Belum memperoleh lencana.</p>
            <p class="em-service-note">Poin dihitung dari skor terbaik kuis dan progres modul belajar. Tersimpan di browser
                ini, bukan sertifikat atau hadiah resmi.</p><a class="editorial-link"
                href="{{ route('creative.academy') }}">Lanjutkan belajar</a>
        </section>
    </div>
@endsection
