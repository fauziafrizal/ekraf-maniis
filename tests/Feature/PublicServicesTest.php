<?php

namespace Tests\Feature;

use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_only_list_published_content_and_scheduled_programs(): void
    {
        $this->withoutVite();
        foreach ([
            ['Terbit Agenda', 'akan_datang', now()->addDay(), now()->subDay()],
            ['Rahasia Agenda', 'akan_datang', now()->addDay(), null],
            ['Wacana Publik', 'wacana', now()->addDay(), now()->subDay()],
        ] as [$title, $status, $start, $published]) {
            Program::create(['title' => $title, 'slug' => Str::slug($title), 'summary' => 'Ringkasan', 'body' => 'Isi', 'audience' => 'Warga', 'status' => $status, 'starts_at' => $start, 'published_at' => $published]);
        }
        $this->get('/layanan')->assertOk()->assertSee('Pusat Layanan Ekraf')->assertSee('Terbit Agenda')->assertSee('Wacana Publik')->assertDontSee('Rahasia Agenda');
        $this->get('/kontak?kategori=Permohonan+pelatihan')->assertOk()->assertSee('value="Permohonan pelatihan" selected', false);
    }
}
