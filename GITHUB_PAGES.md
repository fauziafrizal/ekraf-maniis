# Website Ekraf Maniis di GitHub Pages (gratis)

Konfigurasi ini membuat **versi statis** website Laravel yang sudah ada. Laravel dijalankan saat GitHub Actions membangun halaman, lalu HTML, CSS, JavaScript, logo, dan foto dipublikasikan melalui GitHub Pages.

- URL tujuan: https://fauziafrizal.github.io/ekraf-maniis/
- Laravel asli tetap tersedia di repository.
- Halaman: Beranda, Profil, Potensi Maniis, Subsektor, Program, Kegiatan, Kontak.
- Formulir Kontak menggunakan JavaScript untuk membuka WhatsApp admin; tidak perlu backend.
- Konten yang hanya ada di SQLite lokal MacBook **tidak ikut terbit**. Hanya konten yang ada di kode/seeder repository dan berstatus terbit yang muncul.
- Untuk menambah aktivitas/anggota/subsektor yang tersimpan dalam database, data harus ditambahkan ke seed/kode repository dengan persetujuan publikasi (jangan upload seluruh database lokal).

## Cara mengaktifkan

1. Gabungkan pull request penyiapan GitHub Pages ke `main`.
2. Buka repository GitHub -> **Settings** -> **Pages**.
3. Pada **Build and deployment** -> **Source**, pilih **GitHub Actions**.
4. Buka tab **Actions**, lalu lihat workflow `Publish Ekraf Maniis to GitHub Pages`. Jika perlu, klik **Run workflow** (branch `main`).
5. Jika statusnya hijau (success), buka https://fauziafrizal.github.io/ekraf-maniis/ .

Jika tidak muncul pilihan GitHub Actions, pastikan Actions diizinkan untuk repository.

## Batasan

GitHub Pages tidak menjalankan PHP, MySQL, SQLite, maupun penyimpanan upload dinamis. Perubahan data dari halaman admin Laravel tidak otomatis muncul di publikasi statis. Untuk memperbarui website, simpan perubahan konten dalam repository dan jalankan workflow lagi.

Script hanya menyalin folder publik `images` dan `build`, lalu mengekspor halaman HTML, tanpa menyalin `.env`/database/vendor/konfigurasi PHP.

Jika ada error di Actions, kirim **bagian log error tanpa token/rahasia** agar bisa diperbaiki.
