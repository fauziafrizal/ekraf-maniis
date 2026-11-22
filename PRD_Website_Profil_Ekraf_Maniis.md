# PRD Website Profil Ekraf Maniis

**Produk** — Website profil Koordinator Ekraf Kecamatan Maniis 2026–2031  
**Lokasi** — Kecamatan Maniis, Kabupaten Purwakarta  
**Slogan** — Tumbuh dari Kolaborasi  
**Versi** — 1.0 · 22 November 2026  
**Target implementasi** — Rilis awal website publik yang dapat dijalankan dan diuji secara lokal

## 1. Tujuan produk

Website menjadi rujukan bagi warga, pelaku usaha, sekolah, dan calon mitra yang ingin mengenal Ekraf Maniis. Pengunjung bisa mengetahui siapa pengurusnya, melihat potensi kreatif Maniis, mengikuti perkembangan program, dan menemukan kanal resmi untuk menghubungi pengurus.

Rilis awal berhasil ketika pengunjung yang membuka situs dari ponsel bisa menemukan tiga informasi dari menu utama, yaitu struktur pengurus, status program, dan kontak resmi. Tim pengelola juga harus bisa menyiapkan pembaruan konten dari sumber data yang terdokumentasi.

| Pengguna | Kebutuhan utama |
| --- | --- |
| Warga dan pelaku usaha | Mengenal Ekraf Maniis, melihat program yang sesuai, dan mencari cara menghubungi pengurus. |
| Sekolah dan pelajar | Memahami rencana Ekraf Pelajar Maniis serta melihat sasaran dan statusnya. |
| Pemerintah desa dan calon mitra | Meninjau profil organisasi, potensi wilayah, dan peluang kerja sama. |
| Tim pengelola website | Memperbarui data pengurus, program, kegiatan, dan aset melalui alur pengelolaan yang terdokumentasi. |

## 2. Cakupan rilis

**Rilis awal** mencakup halaman publik berbahasa Indonesia, daftar dan detail program, dokumentasi kegiatan, halaman subsektor, dan kontak resmi. Pengunjung dapat mengakses semuanya tanpa akun. Tim teknis mengelola identitas organisasi melalui konfigurasi dan konten terstruktur melalui database.

**Fase lanjutan** mencakup panel admin agar tim publikasi dapat mengelola konten sendiri. Etalase Maniis, Peta Kreatif Maniis, dan pendataan pelaku kreatif dapat dikembangkan sebagai modul tersendiri setelah pengurus menyepakati alur kerja dan data yang diperlukan.

## 3. Sitemap dan kebutuhan halaman

| Halaman | Rute | Isi dan perilaku |
| --- | --- | --- |
| Beranda | `/` | Identitas, slogan, pengantar singkat, tombol **Lihat Program**, ringkasan potensi, dan cuplikan program serta kegiatan yang sudah diterbitkan. Tampilkan **Hubungi Kami** jika ada minimal satu kanal resmi. |
| Profil | `/profil` | Tentang Ekraf Maniis, masa kepengurusan, profil singkat ketua, dan struktur pengurus pada bagian `#pengurus`. Gunakan nama bidang yang telah disahkan. |
| Potensi Maniis | `/maniis` | Gambaran wilayah, perikanan air tawar dan keramba jaring apung, wisata curug dan pegunungan, serta peluang produk olahan dari buah lokal. |
| Subsektor | `/subsektor` | Daftar subsektor yang sudah disetujui pengurus. Setiap kartu memuat nama, deskripsi ringkas, dan contoh kegiatan atau karya jika tersedia. Jika menampilkan jumlah, hitung dari daftar yang ditayangkan. |
| Program | `/program` | Daftar program terbit dengan judul, ringkasan, sasaran, dan status yang terbaca langsung pada kartu. |
| Detail program | `/program/{slug}` | Uraian program, sasaran, status, dan jadwal atau lokasi jika tersedia. Tautan pendaftaran muncul untuk program yang sudah dikonfirmasi dan memiliki tautan resmi. |
| Kegiatan | `/kegiatan` | Dokumentasi kegiatan terbit, dari yang terbaru, dan bagian `#galeri` untuk foto yang boleh dipublikasikan. |
| Detail kegiatan | `/kegiatan/{slug}` | Judul, tanggal kegiatan, narasi, foto dokumentasi jika tersedia, dan tautan kembali ke daftar kegiatan. |
| Kontak | `/kontak` | Kanal WhatsApp, email, dan media sosial yang digunakan organisasi sesuai ketersediaan. Sertakan cara menghubungi pengurus untuk kolaborasi. |

Menu utama memuat Beranda, Profil, Potensi Maniis, Subsektor, Program, Kegiatan, dan Kontak. Gunakan menu yang dapat dibuka dan ditutup di layar kecil, dengan penanda halaman aktif. Detail program dan kegiatan dapat dibagikan melalui URL masing-masing.

## 4. Alur pengguna

| Kebutuhan | Alur yang harus tersedia |
| --- | --- |
| Mengenal organisasi | Beranda → Profil → Struktur Pengurus. |
| Memeriksa program pelajar | Program → Ekraf Pelajar Maniis → lihat sasaran, status, dan informasi yang sudah dikonfirmasi. |
| Mengajak kerja sama | Beranda atau detail program → Kontak → buka kanal resmi apabila tersedia. |
| Menyiapkan pembaruan | Tim teknis → perbarui konfigurasi atau data konten → verifikasi tampilan lokal → terbitkan data yang sudah disetujui. |

## 5. Konten awal

### Draf teks pengantar beranda

> Kenali potensi kreatif Kecamatan Maniis, temukan program yang sedang dirintis, dan lihat bagaimana warga, pelaku usaha, sekolah, dan komunitas bisa berkarya bersama.

| Bagian | Bahan konten |
| --- | --- |
| Identitas | Koordinator Ekraf Kecamatan Maniis, periode 2026–2031, dengan slogan **Tumbuh dari Kolaborasi**. |
| Struktur | Materi profil mencantumkan Muhamad Kurniawan, S.H. sebagai ketua, Fauzi Afrizal sebagai wakil ketua, dan Nurul Hikmah sebagai sekretaris. Konfirmasi susunan, penulisan nama, bio, dan foto sebelum publikasi. |
| Potensi | Waduk Cirata, perikanan dengan keramba jaring apung, wisata curug dan pegunungan, serta olahan buah lokal seperti nangka dan belimbing. Pasangkan setiap foto dengan lokasi dan kredit yang sesuai. |
| Subsektor | Materi kepengurusan mencantumkan 21 subsektor. Masukkan nama dan urutannya dari daftar yang disetujui. Bila daftarnya belum siap, tampilkan pesan **Daftar subsektor sedang disiapkan.** |
| Wacana program | Pendataan Pelaku Kreatif, Kelas Usaha Kreatif, Ekraf Pelajar Maniis, dan Pasar Kreatif Maniis. Ekraf Pelajar Maniis mengusulkan proyek foto produk, kemasan, atau video pendek untuk pelajar SMP, SMA, dan SMK bersama usaha lokal. |
| Aset dan kanal | Siapkan logo Ekraf Maniis, logo Pemda jika disetujui untuk situs, foto asli Maniis yang dapat dipublikasikan, serta nomor dan akun organisasi yang sudah dikonfirmasi. |

Materi kerja pengurus memuat struktur, potensi, dan usulan program di halaman 5–10. [Ekraf_Maniis_Siap_WhatsApp(1).pdf](https://chatgpt.com/library?file_id=libfile_d71ed1c39ca88191b08843f73a4d4e22&account_id=personal)

## 6. Aturan konten dan publikasi

| Kondisi | Perilaku yang diharapkan |
| --- | --- |
| Data disimpan sebagai draf | `published_at` kosong. Data disembunyikan dari beranda dan daftar. Jika konten memiliki rute detail, akses langsung menghasilkan 404. |
| Data dijadwalkan terbit | Data muncul di daftar dan detail setelah waktu `published_at` tercapai. Urutkan konten terbaru berdasarkan tanggal terbit, dengan urutan tampilan yang dapat ditentukan untuk pengurus, subsektor, dan galeri. |
| Program masih diusulkan | Tampilkan label **Wacana** dan uraian usulannya setelah konten disetujui untuk ditayangkan. Pilihan status selanjutnya adalah **Akan Datang**, **Berjalan**, dan **Selesai**. |
| Program membuka pendaftaran | Tampilkan tombol **Daftar** untuk status **Akan Datang** atau **Berjalan** ketika pengurus memasukkan URL pendaftaran resmi yang aktif. |
| Konten belum tersedia | Halaman daftar tetap terbuka dengan pesan yang sesuai, misalnya **Dokumentasi kegiatan akan ditambahkan di sini.** Beranda menyembunyikan cuplikan yang kosong. |
| Kontak atau foto belum siap | Sembunyikan tautan dan kartu foto yang belum siap. Gunakan nama organisasi pada header dan tata letak berbasis teks pada bagian yang belum memiliki aset. Jika seluruh kanal kosong, tampilkan pesan **Kontak resmi akan diumumkan di halaman ini.** |

Seragamkan tampilan tanggal dalam bahasa Indonesia menggunakan zona waktu Asia/Jakarta. Status program menggambarkan pelaksanaan kegiatan, sedangkan `published_at` mengendalikan kapan kontennya bisa dibaca publik.

## 7. Data dan arsitektur

Gunakan Laravel, Blade, Tailwind CSS, dan Vite, mengikuti versi serta struktur proyek yang tersedia. Untuk pengembangan lokal, SQLite bisa digunakan ketika proyek belum punya database pengembangan yang siap. Jika database dan model sudah tersedia, gunakan konfigurasi dan skema yang kompatibel dengan proyek tersebut. Pertahankan sumber subsektor yang sudah ada, termasuk seeder atau modelnya, jika sesuai kebutuhan.

Simpan nama organisasi, periode, slogan, narasi profil, potensi wilayah, lokasi aset identitas, dan kanal resmi di `config/ekraf.php` atau konfigurasi setara yang sudah tersedia. Gunakan satu sumber tersebut untuk header, footer, halaman publik, dan metadata.

| Entitas | Atribut inti | Aturan |
| --- | --- | --- |
| `board_members` | `name`, `position`, `division`, `bio`, `photo_path`, `sort_order`, `published_at` | Bidang, bio, dan foto opsional. Urutkan struktur sesuai susunan pengurus. |
| `subsectors` | `name`, `slug`, `description`, `examples`, `sort_order`, `published_at` | Nama dan slug unik. Urutan mengikuti daftar yang telah disetujui. |
| `programs` | `title`, `slug`, `summary`, `body`, `audience`, `status`, `starts_at`, `location`, `registration_url`, `published_at` | Slug unik. Jadwal, lokasi, dan tautan pendaftaran opsional. Simpan status sebagai nilai yang konsisten dengan empat label program. |
| `activities` | `title`, `slug`, `summary`, `body`, `event_date`, `cover_path`, `cover_alt`, `cover_credit`, `published_at` | Slug unik. Foto sampul opsional. Foto yang digunakan memiliki teks alternatif dan kredit sesuai sumbernya. |
| `gallery_photos` | `activity_id`, `image_path`, `alt_text`, `caption`, `credit`, `sort_order`, `published_at` | Foto dapat terkait ke satu kegiatan atau berdiri sendiri. Foto yang terkait ke kegiatan baru tampil saat keduanya terbit. |

Mulai dari kontrak data di atas dan petakan ke tabel yang telah ada. Seeder harus dapat dijalankan ulang tanpa menduplikasi konten awal atau menimpa perubahan pengurus. Simpan materi yang menunggu konfirmasi sebagai draf. Dokumentasikan cara menambah konten, menyatakan data siap terbit, dan memasang aset resmi di README.

## 8. Arah desain dan kualitas

- Gunakan tata letak mobile-first dengan navigasi ringkas, judul yang mudah dipindai, dan satu tombol utama pada bagian pembuka. Pakai foto asli Maniis yang disetujui sebagai visual utama jika tersedia.
- Gunakan hijau tua `#173F35`, putih gading `#F8F5ED`, serta emas hangat `#D4A54E` sebagai aksen. Sesuaikan warna dengan logo final dan jaga kontras teks. DM Sans dapat digunakan untuk judul dan isi jika proyek belum memiliki tipografi.
- Beranda dimulai dari nama Ekraf Maniis dan tombol **Lihat Program**, lalu pengantar, potensi, program, dokumentasi, dan kontak. Gunakan label navigasi dan status yang menjelaskan tindakan atau keadaan sebenarnya.
- Pastikan menu, tautan, dan tombol dapat digunakan dengan keyboard, fokus terlihat, dan status program tetap dapat dipahami dari teks. Tetapkan atribut `lang="id"` pada halaman.
- Optimalkan ukuran foto, tetapkan dimensi, dan gunakan pemuatan tertunda untuk foto di bawah layar awal. Gunakan teks alternatif sesuai isi foto dan konteks halaman.
- Siapkan judul halaman dan deskripsi yang spesifik serta metadata Open Graph. Setelah domain publik ditentukan, siapkan URL kanonis dan sitemap untuk halaman yang sudah terbit.

## 9. Urutan implementasi di Codex

1. Periksa instruksi repo, dependensi Laravel, rute, layout Blade, migrasi, dan seeder. Pakai kembali komponen dan data yang relevan. Jika repo kosong, siapkan satu proyek Laravel dengan versi yang kompatibel dengan lingkungan.
2. Tetapkan konfigurasi identitas dan kontrak data. Buat atau sesuaikan model, migrasi, dan seeder sesuai kebutuhan rilis awal. Hubungkan aturan publikasi ke daftar, beranda, detail, dan galeri.
3. Bangun layout publik, navigasi, footer, semua rute di sitemap, dan pesan saat data kosong. Pasang konten serta aset yang tersedia berdasarkan bagian **Konten awal**.
4. Jalankan pengujian Laravel dan build aset. Uji menu di ponsel, tautan publik, detail dengan slug, aturan publikasi, dan kondisi tanpa foto atau kanal kontak.
5. Dokumentasikan perintah instalasi dan menjalankan situs, sumber konten, serta daftar aset yang perlu dilengkapi. Serahkan hasil yang dapat dibuka secara lokal untuk peninjauan pengurus.

## 10. Kriteria penerimaan

- [ ] Semua rute publik dalam sitemap berfungsi. Slug detail yang tidak ditemukan atau belum terbit menghasilkan 404.
- [ ] Pengunjung bisa mencapai halaman profil, program, dan kontak dari menu utama. Uji tata letak pada lebar 360 px, 768 px, dan 1440 px. Menu ponsel bisa dibuka dan ditutup dengan sentuhan serta keyboard. Semua halaman dapat dibaca tanpa gulir horizontal.
- [ ] Nama organisasi, masa kepengurusan, dan slogan tampil konsisten. Materi potensi mencakup perikanan dengan keramba jaring apung, wisata curug dan pegunungan, serta olahan buah lokal.
- [ ] Program yang masih berupa usulan tampil dengan label **Wacana** jika kontennya terbit. Tombol pendaftaran hanya muncul untuk program yang memenuhi aturan di bagian 6.
- [ ] Beranda, daftar, dan rute detail mematuhi aturan waktu publikasi. Daftar kosong dan halaman tanpa aset tetap terbaca, dengan pesan atau tata letak yang sesuai.
- [ ] Kanal kontak yang ditayangkan membuka tujuan yang sesuai. Galeri menggunakan foto yang siap terbit, berikut kredit dan teks alternatifnya.
- [ ] `php artisan test` dan `npm run build` berhasil pada setup lokal yang didokumentasikan. Pengujian mencakup penayangan konten terbit, penolakan akses ke draf dan konten terjadwal, serta cadangan tampilan saat konten kosong.

## 11. Prompt siap salin ke Codex

```text
Baca PRD_Website_Profil_Ekraf_Maniis.md di root proyek. Implementasikan seluruh rilis awal website profil Koordinator Ekraf Kecamatan Maniis 2026–2031 sesuai sitemap, aturan publikasi, dan kriteria penerimaan.

Periksa struktur Laravel, skema database, dan seeder yang tersedia. Gunakan model dan komponen yang sudah sesuai. Jika database lokal belum disiapkan, gunakan SQLite untuk pengembangan. Gunakan materi yang tercantum di PRD dan siapkan tampilan yang sesuai untuk data atau aset resmi yang masih menunggu konfirmasi.

Bangun halaman publik dan navigasi responsif, jalankan pengujian serta build, lalu dokumentasikan cara menjalankan situs dan memperbarui konten. Akhiri dengan perintah untuk membuka situs lokal dan daftar materi yang perlu dilengkapi pengurus.
```
