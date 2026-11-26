# Random Forest — rancangan awal

Tidak ada dataset dummy, hasil akurasi fiktif, atau model siap produksi di folder ini.
Kolom CSV yang diusulkan: marketing, branding, finance, legal, production, label.
Lima skor 1–5 menggambarkan kebutuhan pembinaan dari rendah hingga tinggi. Label harus ditentukan independen oleh pembina/ahli berdasarkan asesmen, bukan dibuat otomatis dari skor yang sama.
Label: pemasaran, merek, keuangan, legalitas, produksi. Sesuaikan variabel dan kelas dengan instrumen penelitian yang disetujui dosen dan mitra sebelum pengumpulan.

Gunakan satu baris per pelaku, data de-identifikasi dan izin yang sesuai. Hindari kebocoran data, duplikasi dan sampel tidak representatif.
Instal di virtual environment Python kompatibel:
```sh
python3 -m venv .venv
source .venv/bin/activate
pip install -r ml/requirements.txt
python ml/train_random_forest.py /path/to/data.csv
```
Tinjau matriks konfusi, balanced accuracy dan metrik per kelas, lakukan validasi tambahan dan konsultasi pembimbing. Hanya setelah disetujui jalankan ulang dengan --approve.
Artefak JSON model disimpan privat; jangan commit dataset atau model berisi data sensitif. Halaman prediksi tidak menyimpan input. Minimum sampel dalam skrip bukan bukti kecukupan statistik. Tidak untuk keputusan penerimaan bantuan.
