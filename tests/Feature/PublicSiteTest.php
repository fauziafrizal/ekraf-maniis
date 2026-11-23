<?php

namespace Tests\Feature;

use App\Enums\ProgramStatus;
use App\Models\Activity;
use App\Models\BoardMember;
use App\Models\GalleryPhoto;
use App\Models\Program;
use App\Models\Subsector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config(['ekraf.contacts' => [], 'ekraf.public_url' => null]);
    }

    public function test_public_pages_have_navigation_and_clear_empty_states(): void
    {
        foreach (['/', '/profil', '/maniis', '/subsektor', '/program', '/kegiatan', '/kontak'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('lang="id"', false)
                ->assertSee('aria-current="page"', false);
        }

        $this->get('/')
            ->assertSee('Koordinator Ekraf Kecamatan Maniis')
            ->assertSee('2026–2031')
            ->assertSee('Tumbuh dari Kolaborasi')
            ->assertSee('Lihat Program')
            ->assertSee('/profil', false)
            ->assertSee('/program', false)
            ->assertSee('/kontak', false)
            ->assertDontSee('rel="canonical"', false)
            ->assertDontSee('Hubungi Kami')
            ->assertDontSee('Lihat semua program')
            ->assertDontSee('Lihat semua kegiatan');

        $this->get('/profil')->assertSee('id="pengurus"', false);
        $this->get('/maniis')
            ->assertSee('Keramba jaring apung')
            ->assertSee('Curug dan pegunungan')
            ->assertSee('belimbing');
        $this->get('/subsektor');
        $this->get('/program')->assertSee('Informasi program akan ditambahkan setelah siap dipublikasikan.');
        $this->get('/kegiatan')
            ->assertSee('id="galeri"', false)
            ->assertSee('Dokumentasi kegiatan akan ditambahkan di sini.');
        $this->get('/kontak')->assertSee('Mari Berkolaborasi Bersama Ekraf Maniis');
    }

    public function test_programs_appear_in_publication_order_when_their_publish_time_arrives(): void
    {
        $now = Carbon::parse('2026-11-22 14:00:00', 'Asia/Jakarta');
        $this->travelTo($now);

        $this->createProgram('Program Lama', ['published_at' => $now->copy()->subDay()]);
        $this->createProgram('Program Baru', [
            'published_at' => $now,
            'starts_at' => '2026-12-01 00:15:00',
            'location' => 'Aula sekolah',
        ]);
        $this->createProgram('Program Esok', ['published_at' => $now->copy()->addDay()]);
        $this->createProgram('Program Rahasia');

        foreach (['/', '/program'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSeeInOrder(['Program Baru', 'Program Lama'])
                ->assertDontSee('Program Esok')
                ->assertDontSee('Program Rahasia');
        }

        $this->get('/program/program-baru')
            ->assertOk()
            ->assertSee('Wacana')
            ->assertSee('Pelajar SMP')
            ->assertSee('1 Desember 2026, 00.15 WIB')
            ->assertSee('datetime="2026-12-01T00:15:00+07:00"', false)
            ->assertSee('Aula sekolah');
        $this->get('/program/program-esok')->assertNotFound();
        $this->get('/program/program-rahasia')->assertNotFound();
        $this->get('/program/program-tidak-ada')->assertNotFound();

        $this->travelTo($now->copy()->addDay());

        $this->get('/program')->assertSeeInOrder(['Program Esok', 'Program Baru', 'Program Lama']);
        $this->get('/program/program-esok')->assertOk();
    }

    public function test_program_timestamps_with_explicit_offsets_are_normalized_to_wib(): void
    {
        $this->travelTo(Carbon::parse('2026-12-01 07:14:59', 'Asia/Jakarta'));
        $program = $this->createProgram('Program Lintas Zona', [
            'published_at' => Carbon::parse('2026-12-01T00:15:00Z'),
            'starts_at' => '2026-12-01T03:30:00+01:00',
        ]);
        $path = '/program/'.$program->slug;

        $this->get($path)->assertNotFound();

        $this->travelTo(Carbon::parse('2026-12-01 07:15:00', 'Asia/Jakarta'));

        $this->get($path)
            ->assertOk()
            ->assertSee('1 Desember 2026, 09.30 WIB')
            ->assertSee('datetime="2026-12-01T09:30:00+07:00"', false);
    }

    public function test_program_registration_requires_an_eligible_status_and_web_url(): void
    {
        $registration = 'https://daftar.example.com/form';

        foreach ([ProgramStatus::Wacana, ProgramStatus::AkanDatang, ProgramStatus::Berjalan, ProgramStatus::Selesai] as $status) {
            $program = $this->createProgram('Program '.$status->value, [
                'status' => $status,
                'registration_url' => $registration,
                'published_at' => now()->subMinute(),
            ]);

            $page = $this->get('/program/'.$program->slug)->assertOk()->assertSee($status->label());

            if ($status === ProgramStatus::AkanDatang || $status === ProgramStatus::Berjalan) {
                $page->assertSee('href="'.$registration.'"', false)->assertSee('Daftar');
            } else {
                $page->assertDontSee($registration, false);
            }
        }

        $this->createProgram('Program Tanpa Tautan', [
            'status' => ProgramStatus::AkanDatang,
            'published_at' => now()->subMinute(),
        ]);
        $this->createProgram('Program Tautan Keliru', [
            'status' => ProgramStatus::Berjalan,
            'registration_url' => '/pendaftaran-bukan-situs-resmi',
            'published_at' => now()->subMinute(),
        ]);

        $this->get('/program/program-tanpa-tautan')->assertOk()->assertDontSee('>Daftar<', false);
        $this->get('/program/program-tautan-keliru')
            ->assertOk()
            ->assertDontSee('/pendaftaran-bukan-situs-resmi', false)
            ->assertDontSee('>Daftar<', false);
    }

    public function test_activities_show_only_published_records_in_latest_order_with_indonesian_dates(): void
    {
        $this->createActivity('Dokumentasi Lama', [
            'event_date' => '2026-10-22',
            'published_at' => now()->subDays(2),
        ]);
        $this->createActivity('Dokumentasi Baru', [
            'event_date' => '2026-10-21',
            'published_at' => now()->subDay(),
        ]);
        $this->createActivity('Dokumentasi Mendatang', ['published_at' => now()->addDay()]);
        $this->createActivity('Dokumentasi Draf');

        foreach (['/', '/kegiatan'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSeeInOrder(['Dokumentasi Baru', 'Dokumentasi Lama'])
                ->assertDontSee('Dokumentasi Mendatang')
                ->assertDontSee('Dokumentasi Draf');
        }

        $this->get('/kegiatan/dokumentasi-baru')
            ->assertOk()
            ->assertSee('21 Oktober 2026')
            ->assertSee('Pelaku usaha berbagi pengalaman.')
            ->assertSee('Kembali ke daftar kegiatan')
            ->assertDontSee('Foto dokumentasi');
        $this->get('/kegiatan/dokumentasi-mendatang')->assertNotFound();
        $this->get('/kegiatan/dokumentasi-draf')->assertNotFound();
        $this->get('/kegiatan/dokumentasi-tidak-ada')->assertNotFound();
    }

    public function test_board_members_and_subsectors_use_the_approved_display_order(): void
    {
        foreach ([['Wakil Uji', 20], ['Ketua Uji', 10], ['Pengurus Belum Terbit', 1]] as [$name, $order]) {
            BoardMember::create([
                'name' => $name,
                'position' => 'Pengurus',
                'division' => 'Bidang Uji',
                'sort_order' => $order,
                'published_at' => $order === 1 ? null : now()->subDay(),
            ]);
        }

        foreach ([['Subsektor Kedua', 'subsektor-kedua', 20], ['Subsektor Pertama', 'subsektor-pertama', 10], ['Subsektor Mendatang', 'subsektor-mendatang', 1]] as [$name, $slug, $order]) {
            Subsector::create([
                'name' => $name,
                'slug' => $slug,
                'description' => 'Keterangan subsektor yang diperiksa.',
                'examples' => 'Contoh karya Uji',
                'sort_order' => $order,
                'published_at' => $order === 1 ? now()->addDay() : now()->subDay(),
            ]);
        }

        $this->get('/profil')->assertSeeInOrder(['Ketua Uji', 'Wakil Uji'])->assertDontSee('Pengurus Belum Terbit');
        $this->get('/subsektor')
            ->assertSeeInOrder(['Subsektor Pertama', 'Subsektor Kedua'])
            ->assertSee('Contoh karya Uji')
            ->assertDontSee('Subsektor Mendatang');
    }

    public function test_gallery_requires_a_published_photo_and_its_linked_activity(): void
    {
        $image = 'test-photo-'.Str::uuid().'.png';
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jKyoAAAAASUVORK5CYII=');
        file_put_contents(public_path($image), $png);

        try {
            $published = $this->createActivity('Kegiatan Publik', [
                'cover_path' => $image,
                'cover_alt' => 'Tempat berlangsungnya kegiatan',
                'cover_credit' => 'Arsip sampul Uji',
                'published_at' => now()->subDay(),
            ]);
            $draft = $this->createActivity('Kegiatan Draf');
            $future = $this->createActivity('Kegiatan Terjadwal', ['published_at' => now()->addDay()]);

            foreach ([
                ['Foto terkait terbit', $published->id, now()->subHour(), 20, $image],
                ['Foto mandiri terbit', null, now()->subDay(), 10, $image],
                ['Foto dari kegiatan draf', $draft->id, now()->subDay(), 1, $image],
                ['Foto dari kegiatan terjadwal', $future->id, now()->subDay(), 1, $image],
                ['Foto dijadwalkan', $published->id, now()->addDay(), 1, $image],
                ['Foto draf', $published->id, null, 1, $image],
                ['Foto tanpa berkas', null, now()->subDay(), 1, 'berkas-yang-belum-tersedia.png'],
            ] as [$caption, $activityId, $publishedAt, $order, $path]) {
                GalleryPhoto::create([
                    'activity_id' => $activityId,
                    'image_path' => $path,
                    'alt_text' => $caption,
                    'caption' => $caption,
                    'credit' => 'Arsip galeri Uji',
                    'sort_order' => $order,
                    'published_at' => $publishedAt,
                ]);
            }

            GalleryPhoto::create([
                'image_path' => $image,
                'alt_text' => 'Foto tanpa kredit',
                'credit' => '',
                'published_at' => now()->subDay(),
            ]);

            $this->get('/kegiatan')
                ->assertOk()
                ->assertSeeInOrder(['Foto mandiri terbit', 'Foto terkait terbit'])
                ->assertSee('alt="Foto terkait terbit"', false)
                ->assertSee('Arsip galeri Uji')
                ->assertDontSee('Foto dari kegiatan draf')
                ->assertDontSee('Foto dari kegiatan terjadwal')
                ->assertDontSee('Foto dijadwalkan')
                ->assertDontSee('Foto draf')
                ->assertDontSee('Foto tanpa berkas')
                ->assertDontSee('Foto tanpa kredit');

            $this->get('/')
                ->assertOk()
                ->assertSee('href="#sorotan-kegiatan"', false)
                ->assertSee('Sorotan Kegiatan')
                ->assertSeeInOrder(['Foto mandiri terbit', 'Foto terkait terbit'])
                ->assertDontSee('Foto dari kegiatan draf')
                ->assertDontSee('Foto dari kegiatan terjadwal')
                ->assertDontSee('Foto dijadwalkan')
                ->assertDontSee('Foto draf')
                ->assertDontSee('Foto tanpa berkas')
                ->assertDontSee('Foto tanpa kredit');

            $this->get('/kegiatan/kegiatan-publik')
                ->assertOk()
                ->assertSee('alt="Tempat berlangsungnya kegiatan"', false)
                ->assertSee('Arsip sampul Uji')
                ->assertSee('Foto terkait terbit')
                ->assertDontSee('Foto mandiri terbit')
                ->assertDontSee('Foto dijadwalkan')
                ->assertDontSee('Foto draf');
        } finally {
            unlink(public_path($image));
        }
    }

    public function test_configured_official_channels_enable_the_home_contact_link(): void
    {
        config([
            'ekraf.contacts.whatsapp' => '+62 812-3456-7890',
            'ekraf.contacts.email' => 'pengurus@example.com',
            'ekraf.contacts.instagram' => 'https://www.instagram.com/ekraf-maniis/',
            'ekraf.contacts.facebook' => '/halaman-tidak-valid',
        ]);

        $this->get('/')->assertOk()->assertSee('Hubungi Kami');
        $this->get('/kontak')
            ->assertOk()
            ->assertSee('Mari Berkolaborasi Bersama Ekraf Maniis')
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee('mailto:pengurus@example.com', false)
            ->assertSee('https://www.instagram.com/ekraf-maniis/', false)
            ->assertSee('Tumbuh dari Kolaborasi')
            ->assertDontSee('Kontak resmi akan diumumkan di halaman ini.');
    }

    public function test_metadata_and_sitemap_use_the_configured_public_domain(): void
    {
        config(['ekraf.public_url' => 'https://ekraf.example.com']);

        $this->createProgram('Program Untuk Publik', ['published_at' => now()->subDay()]);
        $this->createProgram('Program Belum Disetujui');
        $this->createActivity('Cerita Warga Maniis', ['published_at' => now()->subDay()]);
        $this->createActivity('Cerita Warga Terjadwal', ['published_at' => now()->addDay()]);

        $this->get('/program/program-untuk-publik')
            ->assertOk()
            ->assertSee('property="og:title"', false)
            ->assertSee('property="og:description"', false)
            ->assertSee('https://ekraf.example.com/program/program-untuk-publik', false)
            ->assertSee('rel="canonical"', false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('https://ekraf.example.com/profil', false)
            ->assertSee('https://ekraf.example.com/program/program-untuk-publik', false)
            ->assertSee('https://ekraf.example.com/kegiatan/cerita-warga-maniis', false)
            ->assertDontSee('program-belum-disetujui', false)
            ->assertDontSee('cerita-warga-terjadwal', false);
    }

    public function test_rerunning_the_seed_preserves_content_updates_and_publication(): void
    {
        $this->seed();

        $this->assertDatabaseCount('board_members', 3);
        $this->assertDatabaseCount('programs', 4);
        $this->assertSame(0, BoardMember::published()->count());
        $this->assertSame(0, Program::published()->count());
        $this->get('/program/ekraf-pelajar-maniis')->assertNotFound();

        Program::where('slug', 'ekraf-pelajar-maniis')->sole()->update([
            'title' => 'Kolaborasi Pelajar Maniis',
            'slug' => 'kolaborasi-pelajar-maniis',
            'status' => ProgramStatus::AkanDatang,
            'published_at' => now()->subMinute(),
        ]);

        $this->seed();

        $this->assertDatabaseCount('board_members', 3);
        $this->assertDatabaseCount('programs', 4);
        $this->get('/program/ekraf-pelajar-maniis')->assertNotFound();
        $this->get('/program/kolaborasi-pelajar-maniis')
            ->assertOk()
            ->assertSee('Kolaborasi Pelajar Maniis')
            ->assertSee('Akan Datang');
    }

    private function createProgram(string $title, array $attributes = []): Program
    {
        return Program::create(array_replace([
            'title' => $title,
            'slug' => str($title)->slug()->toString(),
            'summary' => 'Proyek kreatif untuk pelajar di Maniis.',
            'body' => 'Pelajar berkolaborasi bersama usaha lokal.',
            'audience' => 'Pelajar SMP',
            'status' => ProgramStatus::Wacana,
        ], $attributes));
    }

    private function createActivity(string $title, array $attributes = []): Activity
    {
        return Activity::create(array_replace([
            'title' => $title,
            'slug' => str($title)->slug()->toString(),
            'summary' => 'Kegiatan bersama pelaku usaha Maniis.',
            'body' => 'Pelaku usaha berbagi pengalaman.',
            'event_date' => '2026-11-21',
        ], $attributes));
    }
}
