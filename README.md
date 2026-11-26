# Website Profil Ekraf Maniis

Website publik Koordinator Ekraf Kecamatan Maniis, Kabupaten Purwakarta, periode 2026–2031. Situs menampilkan profil, potensi wilayah, subsektor, program, kegiatan, galeri, dan kanal resmi organisasi dalam bahasa Indonesia. Aplikasi menggunakan Laravel 13, Blade, Tailwind CSS, dan Vite.

## Menjalankan situs secara lokal

Siapkan PHP 8.4.1+, Composer 2, Node.js 22.12+, npm, serta ekstensi PHP untuk Laravel, termasuk `pdo_sqlite` untuk basis data SQLite lokal. Jalankan perintah berikut dari direktori proyek pada pemasangan pertama.

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm ci
npm run build
php artisan serve --host=127.0.0.1 --port=8010
```

Buka situs di browser. [http://127.0.0.1:8010](http://127.0.0.1:8010)

Untuk proyek yang sudah disiapkan, gunakan kembali `.env` dan basis data yang ada. Jalankan `npm run dev` di terminal lain jika sedang mengubah CSS atau JavaScript.

Jalankan pemeriksaan berikut sebelum serah terima.

```sh
php artisan test
npm run build
```

## Memperbarui identitas dan kanal resmi

Kelola nama organisasi, masa kepengurusan, slogan, narasi beranda dan profil, potensi Maniis, aset identitas, serta kanal resmi melalui `config/ekraf.php`. Simpan hanya informasi yang telah diperiksa pengurus. Periksa hasilnya di beranda, halaman terkait, footer, dan metadata.

Isi `contacts.whatsapp` dengan nomor internasional berkode negara, `contacts.email` dengan alamat resmi, serta `contacts.instagram`, `contacts.facebook`, `contacts.tiktok`, atau `contacts.youtube` dengan URL akun resmi yang tersedia. Gunakan nilai `null` untuk kanal yang belum disahkan. Bila semua kanal kosong, halaman Kontak menampilkan pesan bahwa kontak resmi akan diumumkan.

Saat domain publik sudah ditetapkan, isi `EKRAF_PUBLIC_URL` pada `.env` dengan URL HTTPS resmi. Jalankan `php artisan config:clear` jika konfigurasi pernah di-cache. Periksa URL kanonis, metadata Open Graph, dan `/sitemap.xml`. Sitemap mencantumkan halaman umum dan URL detail yang sudah terbit.

## Memperbarui konten terstruktur

Gunakan `php artisan tinker` dengan basis data lokal yang ditinjau untuk menambahkan atau mengubah data.

| Model | Data yang dikelola |
| --- | --- |
| `App\Models\BoardMember` | Nama, jabatan, bidang yang disahkan, bio, foto opsional, dan urutan pengurus. |
| `App\Models\Subsector` | Nama dan slug unik, deskripsi, contoh kegiatan atau karya, serta urutan sesuai daftar yang disetujui. |
| `App\Models\Program` | Judul, slug unik, ringkasan, uraian, sasaran, status, jadwal, lokasi, dan URL pendaftaran bila tersedia. |
| `App\Models\Activity` | Judul, slug unik, ringkasan, narasi, tanggal kegiatan, serta sampul dengan teks alternatif dan kredit bila tersedia. |
| `App\Models\GalleryPhoto` | Berkas foto, teks alternatif, keterangan dan kredit, urutan, serta `activity_id` bila terkait kegiatan. |

Tambahkan konten dari naskah yang sedang ditinjau dengan `published_at` kosong. Contoh berikut membuat draf subsektor. Ganti nilai contoh dengan materi pengurus.

```php
App\Models\Subsector::create([
    'name' => 'Nama subsektor',
    'slug' => 'nama-subsektor',
    'description' => 'Deskripsi singkat dari materi pengurus.',
    'sort_order' => 1,
    'published_at' => null,
]);
```

Isi `sort_order` pada pengurus, subsektor, dan galeri sesuai urutan tampilan. Gunakan `Ketua`, `Wakil Ketua`, atau `Sekretaris` sebagai nilai `position` untuk kartu pimpinan di halaman Profil. Isi `bio` dan `photo_path` dengan materi yang disetujui jika tersedia. Pertahankan slug yang sudah dibagikan agar tautan lama tetap dapat dibuka. Untuk mengubah rekaman, temukan berdasarkan slug atau ID, ubah atribut yang perlu diperbarui, lalu panggil `save()`.

Setelah pengurus menyetujui konten untuk ditayangkan, isi `published_at` dengan waktu saat ini atau jadwalkan dengan waktu mendatang dalam zona **Asia/Jakarta**. Program dan kegiatan baru muncul di daftar dan halaman detail setelah waktu terbit tercapai. Foto yang terkait dengan kegiatan juga menunggu kegiatan induknya terbit. Untuk menarik konten dari situs, kosongkan kembali `published_at`.

Contoh berikut menayangkan usulan Ekraf Pelajar Maniis setelah pengurus menyetujui publikasinya.

```php
$program = App\Models\Program::where('slug', 'ekraf-pelajar-maniis')->firstOrFail();
$program->published_at = now('Asia/Jakarta');
$program->save();
```

Status program mengikuti tahap pelaksanaan. Isi `status` dengan `wacana` (**Wacana**), `akan_datang` (**Akan Datang**), `berjalan` (**Berjalan**), atau `selesai` (**Selesai**). Tombol **Daftar** tersedia untuk **Akan Datang** atau **Berjalan** dengan URL pendaftaran resmi yang sudah diperiksa. Usulan yang sudah diizinkan tampil dapat diterbitkan dengan status **Wacana**.

Materi awal bersumber dari `PRD_Website_Profil_Ekraf_Maniis.md`. Jalankan `php artisan db:seed` untuk memasukkan materi awal bila diperlukan. `InitialContentSeeder` mengisi tiga pengurus dan empat usulan program sebagai draf serta mempertahankan perubahan pada rekaman awal yang sudah ada. `seed_key` adalah penanda internal bagi rekaman tersebut. Daftar subsektor, kegiatan, dan galeri siap menerima materi yang telah disetujui pengurus.

## Memasang aset resmi

Simpan berkas yang boleh ditayangkan di `public/images/` dan isi path relatif ke `public/`, misalnya `images/waduk-cirata.webp`. Untuk menggunakan penyimpanan Laravel, simpan berkas di `storage/app/public/`, jalankan `php artisan storage:link`, dan isi path seperti `storage/waduk-cirata.webp`. Simpan aset draf di luar kedua direktori publik tersebut. Jika izin tayang dicabut, tarik kontennya dan pindahkan berkas keluar dari direktori publik agar URL berkas juga berhenti berfungsi.

- Isi `assets.logo` untuk logo Ekraf Maniis dan `assets.government_logo` untuk logo Pemda yang diizinkan.
- Isi `assets.hero` atau entri yang sesuai pada `potential` dengan `image`, `alt`, `credit`, dan `location` untuk foto wilayah. Saat mengganti foto pembuka, sesuaikan juga `source_url`, `license`, `license_url`, dan `changes` jika diperlukan.
- Isi `photo_path` pada data pengurus untuk foto yang disetujui.
- Isi `cover_path`, `cover_alt`, dan `cover_credit` bersama-sama untuk sampul kegiatan. Untuk galeri, isi `image_path`, `alt_text`, dan `credit`. Gunakan `caption` untuk informasi tambahan dan `activity_id` untuk mengaitkan foto ke kegiatan.

Gunakan nama berkas sederhana yang terdiri dari huruf tanpa aksen, angka, dan tanda hubung. Gunakan PNG untuk logo. Foto dapat menggunakan JPEG, PNG, WebP, AVIF, atau GIF yang sudah dioptimalkan untuk web. Aplikasi membaca dimensi langsung dari berkas. Cocokkan teks alternatif, lokasi, dan kredit dengan isi serta sumber masing-masing foto. Foto galeri dapat ditinjau di `/kegiatan#galeri` setelah waktu publikasinya tercapai.


Beranda memakai foto berlisensi dari `assets.hero_reference` selama foto utama di `assets.hero` belum lengkap. Foto rujukan Waduk Cirata diambil oleh Annisa 48 pada 23 Mei 2019. Versi situs diperkecil dan bingkai tampilannya disesuaikan. Foto tersebut tetap berlisensi CC BY-SA 4.0. [Foto asli di Wikimedia Commons](https://commons.wikimedia.org/wiki/File:Sunset_di_Waduk_Cirata,_Jawa_Barat.jpg) · [Lisensi foto](https://creativecommons.org/licenses/by-sa/4.0/)

## Musik latar

Website memutar “Musik Degung Sunda” oleh fiikuri dari `public/audio/musik-degung-sunda.mp3` melalui rute `/musik-latar.mp3`. Pemutar mencoba mulai saat halaman dibuka. Jika browser menunggu interaksi, klik atau ketuk laman untuk memulai. Tombol **Musik** menjeda atau melanjutkan audio dan pilihan tersebut tersimpan di browser. [Sumber musik di Pixabay](https://pixabay.com/id/music/dunia-musik-degung-sunda-free-for-use-434888/) · [Lisensi Pixabay](https://pixabay.com/service/license-summary/)

## Materi yang perlu dilengkapi pengurus

- Susunan pengurus yang disahkan, ejaan nama dan jabatan, nama bidang, bio singkat ketua, serta foto yang diizinkan tayang.
- Daftar subsektor yang disetujui beserta urutan, deskripsi, dan contoh karya atau kegiatan jika tersedia.
- Persetujuan penayangan usulan program, sasaran dan status pelaksanaan, jadwal, lokasi, serta tautan pendaftaran resmi jika tersedia.
- Dokumentasi kegiatan beserta judul, tanggal, narasi, kredit foto, dan izin publikasi.
- Logo Ekraf Maniis, logo Pemda bila diizinkan, dan foto asli Maniis dengan informasi lokasi serta kredit pemilik.
- Nomor WhatsApp, alamat email, URL akun media sosial organisasi, dan domain publik yang sudah dikonfirmasi.
- Salinan materi kerja pengurus, khususnya halaman 5–10, untuk mencocokkan struktur, potensi, dan usulan program sebelum disahkan. [Ekraf_Maniis_Siap_WhatsApp(1).pdf](https://chatgpt.com/library?file_id=libfile_d71ed1c39ca88191b08843f73a4d4e22&account_id=personal)

## Struktur kode

- `app/Http/Controllers/`: logika halaman publik dan panel pengurus.
- `app/Models/`: model data organisasi, program, kegiatan, dan katalog.
- `resources/views/layouts/`: layout bersama (header, footer, navigasi).
- `resources/views/components/`: komponen Blade yang digunakan kembali.
- `resources/views/pages/hub/`: modul Smart Creative Hub.
- `resources/css/app.css`: entry point CSS; impor modul desain dan fitur.
- `resources/js/app.js`: entry point JavaScript; impor modul per fungsi.
- `routes/web.php`: rute web publik dan admin.
- `routes/console.php`: perintah Artisan.
- `tests/Feature/`: pengujian fitur.

### Pemeriksaan perubahan

```bash
vendor/bin/pint --test
npm run build
php artisan test
```

Folder vendor, node_modules, dan public/build adalah hasil instalasi atau build; jangan diedit secara manual.

## Sapa Ekraf dan animasi

Asisten menggunakan pencarian informasi publik secara lokal tanpa API. Mode AI generatif opsional dinonaktifkan secara bawaan. Jika pengelola ingin mengaktifkannya, konfigurasi `EKRAF_AI_ENABLED=true`, `EKRAF_AI_MODEL` dan `OPENAI_API_KEY` di `.env` server (jangan commit kunci). Atur `EKRAF_AI_DAILY_LIMIT` sesuai anggaran. Jalankan `php artisan config:clear` setelah perubahan konfigurasi. Pengunjung harus memilih mode AI eksternal sebelum pertanyaan dikirim ke penyedia API. Integrasi eksternal dapat menimbulkan biaya; uji dengan akun dan model yang diizinkan sebelum publikasi. Pencarian lokal tetap tersedia saat API tidak aktif atau gagal. Integrasi membutuhkan server Laravel aktif, bukan hosting statis saja.

## Modul ekspansi

- `/komunitas`: kiriman moderasi untuk forum, pameran, portofolio, challenge, kompetisi dan kolaborasi.
- `/pengurus/sekretariat`: arsip PDF privat, surat, notulen, penerbitan/pencabutan QR, voting internal per akun pengurus, moderasi, dan survei.
- `/katalog/{id}`: detail katalog, foto, QR, toko, ulasan.
- `/peta-umkm`: Leaflet untuk koordinat yang diizinkan pemilik; Pannellum memerlukan foto equirectangular asli berizin di `public/images/panorama-maniis.jpg`.
- `/creative-challenge`: kuis, poin dan lencana lokal; `/notifikasi`: pengingat hanya saat halaman terbuka.
- `/studio-kreatif`: draf lokal; AI eksternal mengikuti konfigurasi dan persetujuan sebelumnya.
- `/klasifikasi-pembinaan`: pipeline Random Forest dalam `ml/`, tidak aktif sebelum model disetujui (lihat `ml/README.md`).

Sebelum produksi: jalankan migrasi, buat akun pengurus dengan `php artisan ekraf:admin-create`, gunakan HTTPS dan domain resmi, pastikan QR dibuat dari domain resmi, tinjau kebijakan privasi, backup database dan file privat, lakukan pengujian perangkat asli. Jangan menyimpan data riset, model, atau API key pada repository publik. Semua fitur database memerlukan hosting Laravel aktif.

## Sambutan suara Sapa Ekraf

Narasi berada di `config/ekraf.php` (`welcome_narration`). Pemutaran hanya terjadi setelah pengunjung menekan tombol. Musik latar dijeda saat sambutan. Browser mencoba suara Indonesia terbaik yang tersedia jika tidak ada file audio. Kualitas dan ketersediaan bergantung pada perangkat. Untuk suara yang konsisten, siapkan file hasil TTS neural berizin `public/audio/sapa-ekraf-welcome.mp3` atau `.m4a` sesuai narasi. Periksa izin penggunaan dan dengarkan hasil sebelum publikasi. Teks tetap tersedia bagi pengguna tanpa audio.
