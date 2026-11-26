@extends('layouts.public')
@section('content')
    <x-page-heading :title="$pageTitle" eyebrow="Ruang belajar kreatif" :description="$pageDescription" />
    <div class="site-shell em-service-layout">
        <section>
            <h2 class="section-title">{{ $students ? 'Ide proyek pelajar' : 'Modul belajar mandiri' }}</h2>
            <div class="em-hub-grid">
                @foreach ($lessons as $lesson)
                    <article class="em-service-card">
                        <h3>{{ $lesson[0] }}</h3>
                        <p>{{ $lesson[1] }}</p>
                        <details class="em-lesson">
                            <summary>Latihan praktik</summary>
                            <p>{{ $lesson[2] }}</p>
                        </details>
                        <label class="em-progress" hidden><input type="checkbox"
                                data-em-lesson="{{ $students ? 'student' : 'academy' }}-{{ $loop->index }}"> Tandai
                            selesai</label>
                    </article>
                @endforeach
            </div>
            <p class="em-service-note">Materi ini merupakan panduan mandiri, bukan pelatihan bersertifikat. Progres hanya
                tersimpan di browser Anda.</p>
            <a class="button-primary"
                href="{{ route('contact', ['kategori' => $students ? 'Kerja sama atau kolaborasi' : 'Permohonan pelatihan']) }}#form-kolaborasi">{{ $students ? 'Ajukan kolaborasi sekolah' : 'Ajukan kebutuhan pelatihan' }}</a>
        </section>
        @unless ($students)
            <section aria-labelledby="em-cost-title">
                <h2 class="section-title" id="em-cost-title">Kalkulator harga jual</h2>
                <p class="prose-copy">Hitung harga berdasarkan biaya produksi per unit dan markup yang diinginkan. Belum
                    termasuk pajak atau biaya lain yang tidak Anda masukkan.</p>
                <form class="em-admin-form em-service-card" data-em-calculator hidden>
                    @foreach (['materials' => 'Bahan baku per unit (Rp)', 'labor' => 'Tenaga kerja per unit (Rp)', 'packaging' => 'Kemasan per unit (Rp)', 'overhead' => 'Biaya lain per unit (Rp)', 'markup' => 'Markup atas biaya (%)'] as $key => $label)
                        <label for="calc-{{ $key }}">{{ $label }}</label><input type="number"
                            id="calc-{{ $key }}" name="{{ $key }}" min="0"
                            max="{{ $key === 'markup' ? 10000 : 1000000000 }}" step="any" required
                            value="{{ $key === 'markup' ? 20 : 0 }}">
                    @endforeach
                    <button class="button-primary">Hitung harga</button>
                    <output data-em-calculator-output role="status"></output>
                </form>
            </section>
        @endunless
        <section>
            <h2 class="section-title">Agenda dan program</h2>
            <p>Informasi kegiatan resmi dapat ditemukan melalui agenda yang sudah dipublikasikan.</p><a
                class="editorial-link" href="{{ route('services') }}#agenda">Lihat agenda</a>
        </section>
    </div>
@endsection
