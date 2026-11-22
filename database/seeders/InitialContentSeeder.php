<?php

namespace Database\Seeders;

use App\Enums\ProgramStatus;
use App\Models\BoardMember;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedBoardMembers();
            $this->seedPrograms();
        });
    }

    private function seedBoardMembers(): void
    {
        $members = [
            'ketua' => [
                'name' => 'Muhamad Kurniawan, S.H.',
                'position' => 'Ketua',
                'sort_order' => 10,
            ],
            'wakil-ketua' => [
                'name' => 'Fauzi Afrizal',
                'position' => 'Wakil Ketua',
                'sort_order' => 20,
            ],
            'sekretaris' => [
                'name' => 'Nurul Hikmah',
                'position' => 'Sekretaris',
                'sort_order' => 30,
            ],
        ];

        foreach ($members as $key => $member) {
            if (BoardMember::query()->where('name', $member['name'])->where('position', $member['position'])->exists()) {
                continue;
            }

            BoardMember::query()->firstOrCreate(
                ['seed_key' => $key],
                [...$member, 'published_at' => null],
            );
        }
    }

    private function seedPrograms(): void
    {
        $programs = [
            'pendataan-pelaku-kreatif' => [
                'title' => 'Pendataan Pelaku Kreatif',
                'summary' => 'Usulan pendataan pelaku ekonomi kreatif di Kecamatan Maniis.',
                'body' => 'Pendataan ini diusulkan untuk mengenali pelaku, karya, dan kebutuhan usaha kreatif di Maniis. Hasil pendataan dapat menjadi bahan untuk merancang kegiatan dan membuka peluang kolaborasi.',
                'audience' => 'Pelaku usaha, individu, dan komunitas kreatif di Kecamatan Maniis.',
            ],
            'kelas-usaha-kreatif' => [
                'title' => 'Kelas Usaha Kreatif',
                'summary' => 'Usulan kelas pengembangan keterampilan bagi pelaku usaha kreatif.',
                'body' => 'Kelas ini diusulkan sebagai ruang belajar bersama bagi pelaku usaha. Bentuk dan materi kelas dapat disusun bersama pelaku usaha sesuai kebutuhan yang ditemukan di Maniis.',
                'audience' => 'Pelaku usaha dan warga yang sedang merintis usaha kreatif di Kecamatan Maniis.',
            ],
            'ekraf-pelajar-maniis' => [
                'title' => 'Ekraf Pelajar Maniis',
                'summary' => 'Usulan proyek kreatif untuk pelajar bersama usaha lokal di Maniis.',
                'body' => 'Pelajar SMP, SMA, dan SMK diusulkan bekerja bersama usaha lokal melalui proyek foto produk, kemasan, atau video pendek. Proyek ini memberi pelajar kesempatan untuk berkarya sambil mengenal kebutuhan usaha di lingkungannya.',
                'audience' => 'Pelajar SMP, SMA, dan SMK serta usaha lokal yang berkolaborasi di Maniis.',
            ],
            'pasar-kreatif-maniis' => [
                'title' => 'Pasar Kreatif Maniis',
                'summary' => 'Usulan ruang pertemuan warga dengan karya dan produk kreatif Maniis.',
                'body' => 'Pasar ini diusulkan sebagai ruang bagi pelaku kreatif untuk memperkenalkan karya dan produk, bertemu warga, dan menjajaki kerja sama dengan pelaku lain.',
                'audience' => 'Pelaku usaha, komunitas kreatif, dan warga Kecamatan Maniis.',
            ],
        ];

        foreach ($programs as $slug => $program) {
            if (Program::query()->where('slug', $slug)->exists()) {
                continue;
            }

            Program::query()->firstOrCreate(
                ['seed_key' => $slug],
                [...$program, 'slug' => $slug, 'status' => ProgramStatus::Wacana, 'published_at' => null],
            );
        }
    }
}
