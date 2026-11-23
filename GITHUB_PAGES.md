# Publikasi Ekraf Maniis di GitHub Pages

GitHub Actions menjalankan Laravel dengan basis data SQLite sementara, membangun aset Vite, lalu mengekspor halaman yang dapat diakses publik. GitHub Pages menyajikan hasilnya sebagai situs statis. [Buka situs Ekraf Maniis](https://fauziafrizal.github.io/ekraf-maniis/)

## Terbitkan pembaruan

1. Simpan perubahan tampilan, konfigurasi, aset, dan materi yang telah disetujui ke repository.
2. Pada **Settings → Pages**, pilih **GitHub Actions** sebagai sumber publikasi jika pengaturan ini belum aktif.
3. Gabungkan perubahan ke `main`. Workflow **Publish Ekraf Maniis to GitHub Pages** berjalan saat ada perubahan di branch itu. Untuk membangun ulang secara manual, buka **Actions**, pilih workflow tersebut, lalu pilih **Run workflow** dengan branch `main`.
4. Setelah publikasi berhasil, buka situs dan periksa navigasi, foto, serta pemutar musik. [Buka situs Ekraf Maniis](https://fauziafrizal.github.io/ekraf-maniis/)

## Perbarui konten

Ikuti [README](README.md) untuk menyiapkan data dan aset. Workflow menjalankan seeder yang disimpan di repository, termasuk `GitHubPagesProfileSeeder` untuk foto pimpinan dan `GitHubPagesGallerySeeder` untuk galeri yang disetujui. Untuk menayangkan perubahan basis data lokal, tambahkan materi yang telah disetujui ke seeder publikasi dan bangun ulang situs. Program, kegiatan, dan foto mengikuti waktu terbit pada saat workflow dijalankan.

Formulir kolaborasi menggunakan JavaScript untuk membuka WhatsApp. Perubahan di basis data Laravel perlu dimasukkan ke sumber publikasi sebelum hasilnya bisa tampil di GitHub Pages.

## Tinjau hasil ekspor

Siapkan Laravel sesuai README. Gunakan basis data uji yang sudah diisi dengan seeder publikasi, jalankan `npm run build`, lalu biarkan server Laravel berjalan di port 8000. Dari terminal lain, jalankan perintah berikut dari root proyek.

```sh
python3 scripts/export_github_pages.py --output _site
```

Folder `_site` berisi halaman HTML, sitemap, aset Vite, foto publik, dan `musik-latar.mp3` yang diambil dari rute audio Laravel. URL lokal dan aset CSS menggunakan prefiks proyek `/ekraf-maniis`. Berkas `.env`, basis data, dependensi Composer, dan sumber PHP tetap di luar hasil ekspor.
