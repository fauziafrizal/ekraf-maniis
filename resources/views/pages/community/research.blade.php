@extends('layouts.public')
@section('content')
    <x-page-heading title="Klasifikasi Kebutuhan Pembinaan" eyebrow="Random Forest · Riset"
        description="Modul penelitian untuk membantu mengidentifikasi kebutuhan utama pembinaan pelaku ekonomi kreatif." />
    <div class="site-shell em-service-layout">
        <section class="em-tool-card">
            @if (!$available)
                <h2 class="section-title">Model penelitian belum tersedia</h2>
                <p>Pelatihan memerlukan dataset nyata dengan label pembinaan yang ditetapkan secara independen dan hasil
                    validasi yang ditinjau. Tidak ada hasil prediksi atau akurasi yang dibuat-buat.</p><a
                    class="button-primary" href="{{ route('creative.toolkit') }}#rekomendasi">Gunakan rekomendasi berbasis
                    aturan</a>
            @else
                <h2 class="section-title">Asesmen awal</h2>
                <p>Skor 1 berarti kebutuhan rendah dan 5 kebutuhan tinggi. Hasil bersifat pendukung, bukan keputusan
                    pembinaan atau bantuan.</p>
                <form method="post" action="{{ route('community.research.predict') }}" class="em-admin-form">@csrf
                    @foreach (['marketing' => 'Pemasaran', 'branding' => 'Kemasan dan merek', 'finance' => 'Keuangan', 'legal' => 'Legalitas', 'production' => 'Produksi'] as $key => $label)
                        <label for="rf-{{ $key }}">{{ $label }}</label><select name="{{ $key }}"
                            id="rf-{{ $key }}" required>
                            @foreach (range(1, 5) as $score)
                                <option value="{{ $score }}" @selected(old($key) == $score)>{{ $score }}
                                </option>
                            @endforeach
                        </select>
                    @endforeach
                    <button class="button-primary">Klasifikasikan kebutuhan</button>
                </form>
                @if (session('rf_result'))
                    <p class="em-tool-result">Kelas prediksi: {{ session('rf_result.label') }} · Rata-rata probabilitas
                        hutan: {{ number_format(session('rf_result.score') * 100, 1, ',', '.') }}%. Skor ini bukan jaminan
                        kebenaran.</p>
                @endif
            @endif
        </section>
    </div>
@endsection
