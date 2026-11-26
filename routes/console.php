<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ekraf:admin-create', function () {
    $email = trim((string) $this->ask('Email pengurus'));
    $name = trim((string) $this->ask('Nama pengurus'));
    $password = (string) $this->secret('Kata sandi baru (minimal 12 karakter)');
    if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ! $name || mb_strlen($password) < 12 || strlen($password) > 72 || User::where('email', $email)->exists()) {
        $this->error('Data tidak valid atau email sudah digunakan. Tidak ada akun yang dibuat.');

        return 1;
    }
    $user = new User(['email' => $email, 'name' => $name, 'password' => $password]);
    $user->is_ekraf_admin = true;
    $user->save();
    $this->info('Akun pengurus dibuat.');

    return 0;
})->purpose('Membuat akun pengurus Ekraf dengan kata sandi yang dimasukkan secara privat.');
