@extends('layouts.public')
@section('content')
    <x-page-heading title="Login pengurus" description="Akses terbatas untuk mengelola publikasi usaha dan produk." />
    <div class="site-shell em-service-layout">
        <form method="post" action="{{ route('creative.login') }}" class="em-admin-form em-service-card">
            @csrf
            @if ($errors->any())
                <p role="alert">{{ $errors->first() }}</p>
            @endif
            <label for="admin-email">Email pengurus</label><input type="email" id="admin-email" name="email"
                value="{{ old('email') }}" autocomplete="username" required>
            <label for="admin-password">Kata sandi</label><input type="password" id="admin-password" name="password"
                autocomplete="current-password" required>
            <button class="button-primary">Masuk</button>
        </form>
    </div>
@endsection
