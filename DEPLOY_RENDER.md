# Ekraf Maniis — Deploy Gratis ke Render (uji coba)

Konfigurasi ini menyiapkan proyek Laravel 13 dan Vite untuk **Render Web Service / Docker / Free**. Tidak perlu Codex.

## Di GitHub

Gabungkan (merge) pull request penyiapan Docker ke branch `main` sebelum membuat deployment.

## Di Render

1. New > Web Service, pilih repository `fauziafrizal/ekraf-maniis`.
2. Name: `ekraf-maniis`.
3. Language: `Docker`, Branch: `main`.
4. Root Directory: **kosong**.
5. Dockerfile Path: `./Dockerfile` (bukan titik `.` saja).
6. Compute: **Free ($0/month)**.
7. Isi Environment Variables sesuai tabel di bawah.
8. Klik **Deploy web service**. Periksa **Logs** bila ada error.

| Key | Value |
| --- | --- |
| APP_NAME | Ekraf Maniis |
| APP_ENV | production |
| APP_DEBUG | false |
| APP_URL | https://ekraf-maniis.onrender.com |
| EKRAF_PUBLIC_URL | https://ekraf-maniis.onrender.com |
| APP_KEY | **Kunci rahasia hasil `php artisan key:generate --show`, jangan dipublikasikan** |
| DB_CONNECTION | sqlite |
| SESSION_DRIVER | file |
| CACHE_STORE | file |
| QUEUE_CONNECTION | sync |
| LOG_CHANNEL | stderr |

`APP_URL` dan `EKRAF_PUBLIC_URL` harus disesuaikan jika alamat yang diberikan Render berbeda. Di Render, `PORT` biasanya sudah diatur otomatis dan tidak perlu dibuat manual.

Buat APP_KEY di terminal lokal pada folder proyek (perlu PHP dan dependensi Laravel):

```bash
php artisan key:generate --show
```

Salin hasilnya langsung ke pengaturan **APP_KEY** di Render, **jangan** masukkan nilainya ke GitHub, screenshot, atau chat.

## Batasan paket Free

Render Free menggunakan **filesystem sementara**. Berkas SQLite, database, foto upload, dan perubahan lain pada disk container dapat hilang saat spin-down, restart, atau redeploy. Konfigurasi ini secara sengaja menginisialisasi data awal setelah startup **hanya untuk pratinjau situs**, bukan database produksi permanen.

Free Web Service juga dapat tidur setelah tidak aktif. Pengunjung pertama setelah tidur mungkin perlu menunggu.

Sebelum digunakan sebagai portal organisasi dengan data yang harus tetap tersimpan, hubungkan ke database dan penyimpanan berkas yang **persisten** serta rencanakan backup.

## Masalah umum

- **Dockerfile not found:** pastikan PR sudah di-merge dan path di Render `./Dockerfile`.
- **APP_KEY must be set:** isi APP_KEY di Environment Variables Render.
- **Gagal migrasi:** lihat error di Logs; jangan menggunakan `migrate:fresh` pada database berisi data.
- **CSS tidak tampil:** pastikan tahap `npm run build` berhasil pada Docker build.
- **Situs tidak terbuka:** periksa Log dan kunjungi rute `/up` untuk health check.

File baru di PR ini tidak mengubah desain, gambar, route, controller, atau data lokal.
