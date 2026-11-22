<?php

namespace Database\Seeders;

use App\Models\BoardMember;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Publish only the three approved leadership portraits for the static Pages
 * export. Keep the normal Laravel database seeder's unpublished default.
 */
class GitHubPagesProfileSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name' => 'Muhamad Kurniawan, S.H.', 'position' => 'Ketua', 'photo' => 'images/ketua.jpg'],
            ['name' => 'Fauzi Afrizal', 'position' => 'Wakil Ketua', 'photo' => 'images/wakil.jpeg'],
            ['name' => 'Nurul Hikmah', 'position' => 'Sekretaris', 'photo' => 'images/sekertaris.jpg'],
        ];

        foreach ($members as $item) {
            if (! is_file(public_path($item['photo']))) {
                throw new RuntimeException('Foto profil tidak ditemukan: '.$item['photo']);
            }

            $member = BoardMember::query()
                ->where('name', $item['name'])
                ->where('position', $item['position'])
                ->firstOrFail();

            $member->photo_path = $item['photo'];
            $member->published_at = now()->subMinute();
            $member->save();
        }
    }
}
