@extends('layouts.public')
@section('content')
    <x-page-heading title="Survei Kepuasan Pengunjung"
        description="Bantu pengurus meningkatkan layanan website. Tidak perlu nama, email, atau nomor telepon." />
    <div class="site-shell em-service-layout">
        <section class="em-tool-card">
            @if (session('status'))
                <p role="status">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <p role="alert">{{ $errors->first() }}</p>
            @endif
            <form action="{{ route('community.feedback.submit') }}" method="post" class="em-admin-form">@csrf
                <label for="survey-rating">Seberapa puas Anda terhadap website ini?</label><select id="survey-rating"
                    name="rating" required>
                    <option value="">Pilih</option>
                    @foreach (range(5, 1) as $rating)
                        <option value="{{ $rating }}">{{ $rating }} / 5</option>
                    @endforeach
                </select>
                <label for="survey-body">Saran (opsional)</label>
                <textarea name="body" id="survey-body" rows="5" maxlength="1000" placeholder="Hindari data pribadi"></textarea>
                <button class="button-primary">Kirim masukan</button>
            </form>
        </section>
    </div>
@endsection
