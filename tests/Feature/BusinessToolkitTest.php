<?php

namespace Tests\Feature;

use App\Models\CreativeListing;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessToolkitTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_toolkit_and_private_listing_exclusion(): void
    {
        CreativeListing::create(['kind' => 'product', 'name' => 'Produk Terbit', 'category' => 'Kriya', 'description' => 'Uji', 'published' => true]);
        CreativeListing::create(['kind' => 'product', 'name' => 'Produk Rahasia', 'category' => 'Kriya', 'description' => 'Uji', 'published' => false]);
        $this->get('/alat-usaha')->assertOk()->assertSee('Kalkulator titik impas')->assertSee('Checklist kesiapan usaha');
        $this->get('/ruang-saya')->assertOk()->assertSee('Produk Terbit')->assertDontSee('Produk Rahasia');
        $this->get('/hub')->assertOk()->assertSee('Creative Business Toolkit');
    }

    public function test_calendar_only_shows_published_scheduled_programs_and_exports_utc(): void
    {
        $base = ['summary' => 'Ringkasan, uji; kalender', 'body' => 'Uji', 'audience' => 'Warga', 'starts_at' => '2026-12-01 09:30:00', 'published_at' => now()->subDay(), 'status' => 'akan_datang'];
        $program = Program::create(['title' => 'Agenda Terbit', 'slug' => 'agenda-terbit'] + $base);
        Program::create(array_replace($base, ['title' => 'Agenda Rahasia', 'slug' => 'agenda-rahasia', 'published_at' => null]));
        Program::create(array_replace($base, ['title' => 'Agenda Wacana', 'slug' => 'agenda-wacana', 'status' => 'wacana']));
        Program::create(array_replace($base, ['title' => 'Bulan Lain', 'slug' => 'bulan-lain', 'starts_at' => '2027-01-01 09:30:00']));
        $this->get('/kalender?bulan=2026-12')->assertOk()->assertSee('Agenda Terbit')->assertDontSee('Agenda Rahasia')->assertDontSee('Agenda Wacana')->assertDontSee('Bulan Lain');
        $this->get('/kalender?bulan=2026-13')->assertNotFound();
        $this->get('/kalender/agenda-terbit.ics')->assertOk()->assertHeader('Content-Type', 'text/calendar; charset=UTF-8')->assertSee('DTSTART:20261201T023000Z', false)->assertSee('SUMMARY:Agenda Terbit', false);
        $this->get('/kalender/agenda-rahasia.ics')->assertNotFound();
        $this->get('/kalender/agenda-wacana.ics')->assertNotFound();
    }
}
