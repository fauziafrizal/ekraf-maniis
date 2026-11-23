<?php

namespace Tests\Feature;

use App\Models\BoardMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadershipProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_features_one_published_member_per_leadership_role(): void
    {
        $this->withoutVite();

        foreach ([
            ['Sekretaris Uji', 'Sekertaris', 30, now()],
            ['Ketua Uji', 'Ketua', 10, now()],
            ['Wakil Ketua Uji', 'Wakil Ketua', 20, now()],
            ['Ketua Bidang Uji', 'Ketua Bidang Riset', 40, now()],
            ['Pengurus Draf', 'Ketua', 1, null],
            ['Pengurus Mendatang', 'Wakil Ketua', 1, now()->addDay()],
        ] as [$name, $position, $sortOrder, $publishedAt]) {
            BoardMember::create([
                'name' => $name,
                'position' => $position,
                'sort_order' => $sortOrder,
                'published_at' => $publishedAt,
            ]);
        }

        $response = $this->get('/profil')
            ->assertOk()
            ->assertSee('data-leadership', false)
            ->assertSee('data-leader-role="ketua"', false)
            ->assertSee('data-leader-role="wakil-ketua"', false)
            ->assertSee('data-leader-role="sekretaris"', false)
            ->assertSeeInOrder(['Ketua Uji', 'Wakil Ketua Uji', 'Sekretaris Uji', 'Ketua Bidang Uji'])
            ->assertDontSee('Sekertaris')
            ->assertDontSee('Pengurus Draf')
            ->assertDontSee('Pengurus Mendatang');

        $this->assertSame(1, substr_count($response->getContent(), '>Ketua Uji<'));
        $this->assertSame(1, substr_count($response->getContent(), '>Ketua Bidang Uji<'));
    }
}
