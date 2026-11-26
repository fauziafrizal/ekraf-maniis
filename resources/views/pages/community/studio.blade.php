@extends('layouts.public')
@section('content')
    <x-page-heading title="AI Creative Studio"
        description="Susun ide usaha dan deskripsi produk. Periksa kembali hasil sebelum digunakan." />
    <div class="site-shell em-service-layout">
        <section class="em-tool-card">
            <h2 class="section-title">Creative Generator</h2>
            @if ($errors->any())
                <p role="alert">{{ $errors->first() }}</p>
            @endif
            <form action="{{ route('community.studio.generate') }}" method="post" class="em-admin-form">@csrf
                <label for="studio-kind">Pilih kebutuhan</label><select name="kind" id="studio-kind">
                    <option value="idea" @selected(old('kind') === 'idea')>Business Idea Generator</option>
                    <option value="description" @selected(old('kind') === 'description')>Product Description</option>
                </select>
                <label for="studio-topic">Produk atau bidang usaha</label><input id="studio-topic" name="topic"
                    maxlength="100" required value="{{ old('topic') }}">
                <label for="studio-facts">Potensi, keterampilan atau fakta produk</label>
                <textarea id="studio-facts" name="facts" maxlength="600" required rows="5">{{ old('facts') }}</textarea>
                @if (config('services.ekraf_ai.enabled') &&
                        filled(config('services.ekraf_ai.key')) &&
                        filled(config('services.ekraf_ai.model')))
                    <label><input type="checkbox" name="allow_ai" value="1"> Gunakan AI eksternal; masukan dikirim ke
                    penyedia AI.</label>@else<p class="em-service-note">AI eksternal belum dikonfigurasi. Draf
                        dihasilkan menggunakan template.</p>
                @endif
                <button class="button-primary">Buat draf</button>
            </form>
            @if (session('studio_result'))
                <div class="em-tool-result">
                    <p>Mode: {{ session('studio_mode') === 'ai' ? 'AI generatif' : 'Template lokal' }}</p>
                    <p class="em-prewrap">{{ session('studio_result') }}</p>
                </div>
            @endif
            <p>Jangan masukkan data pribadi atau rahasia usaha.</p>
        </section>
        <section class="em-tool-card">
            <h2 class="section-title">Klasifikasi kebutuhan pembinaan</h2>
            <p>Model Random Forest hanya dapat digunakan setelah pelatihan dan validasi dengan data berlabel yang sah.
                Rekomendasi berbasis aturan tetap tersedia.</p><a class="button-primary"
                href="{{ route('community.research') }}">Buka modul Random Forest</a>
        </section>
    </div>
@endsection
