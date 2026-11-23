<?php

namespace Database\Seeders;

use App\Models\GalleryPhoto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GitHubPagesGallerySeeder extends Seeder
{
    public function run(): void
    {
        $photos = [
            'images/kegiatan/pelantikan.webp' => [
                'alt_text' => 'Peserta pelantikan berfoto bersama dalam barisan lebar di pendopo beratap kayu.',
                'caption' => 'Pelantikan',
                'sort_order' => 0,
                'published_at' => '2026-11-23 17:10:28',
            ],
            'images/kegiatan/pelantikan-foto-bersama.webp' => [
                'alt_text' => 'Tampak lebih dekat barisan peserta pelantikan, sebagian mengenakan batik dan selempang.',
                'caption' => 'Foto bersama pelantikan',
                'sort_order' => 0,
                'published_at' => '2026-11-23 17:10:28',
            ],
            'images/kegiatan/diskusi-meja-pertemuan.jpg' => [
                'alt_text' => 'Tiga orang duduk di meja kayu. Seorang peserta berbaju merah marun berbicara dengan dua peserta di sebelah kiri.',
                'caption' => 'Diskusi di meja pertemuan',
                'sort_order' => 1,
                'published_at' => '2026-11-23 16:53:25',
            ],
            'images/kegiatan/diskusi-sudut-lebar.jpg' => [
                'alt_text' => 'Tiga orang berdiskusi di ujung meja kayu panjang, terlihat dari samping ruang pertemuan.',
                'caption' => 'Suasana ruang pertemuan',
                'sort_order' => 2,
                'published_at' => '2026-11-23 16:53:25',
            ],
        ];

        DB::transaction(function () use ($photos): void {
            foreach ($photos as $path => $photo) {
                if (! is_file(public_path($path))) {
                    throw new RuntimeException('Foto galeri tidak ditemukan: '.$path);
                }

                GalleryPhoto::query()->firstOrCreate(['image_path' => $path], $photo + [
                    'activity_id' => null,
                    'credit' => 'Kiriman untuk situs Ekraf Maniis',
                ]);
            }
        });
    }
}
