<?php

namespace Tests\Feature;

use App\Models\CreativeListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreativeHubTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_all_public_modules_render(): void
    {
        foreach (['/hub', '/marketplace', '/direktori', '/akademi', '/ekraf-pelajar', '/data-center', '/peta', '/bantuan', '/pengurus/login'] as $path) {
            $this->get($path)->assertOk();
        }
        $this->get('/data-center/statistik.csv')->assertOk()->assertDownload('ekraf-maniis-statistik-publik.csv');
    }

    public function test_drafts_are_private_and_counts_are_real(): void
    {
        $fields = ['category' => 'Kriya', 'description' => 'Deskripsi', 'kind' => 'product'];
        CreativeListing::create($fields + ['name' => 'Produk Terbit', 'published' => true]);
        CreativeListing::create($fields + ['name' => 'Produk Rahasia', 'published' => false]);
        CreativeListing::create(['kind' => 'business', 'name' => 'Usaha Terbit', 'category' => 'Kriya', 'description' => 'Deskripsi', 'village' => 'Desa Uji', 'published' => true]);
        $this->get('/marketplace')->assertSee('Produk Terbit')->assertDontSee('Produk Rahasia')->assertDontSee('Usaha Terbit');
        $this->get('/direktori')->assertSee('Usaha Terbit')->assertDontSee('Produk Terbit');
        $this->get('/data-center')->assertSee('Desa Uji')->assertDontSee('Produk Rahasia');
        $this->assertStringContainsString('"Produk publik",1', $this->get('/data-center/statistik.csv')->streamedContent());
    }

    public function test_admin_permission_and_publication_consent(): void
    {
        $this->get('/pengurus')->assertRedirect(route('login'));
        $user = User::factory()->create();
        $this->actingAs($user)->get('/pengurus')->assertForbidden();
        $this->post('/pengurus/katalog', [])->assertForbidden();
        $admin = User::factory()->create(['password' => 'testing-password-123']);
        $admin->is_ekraf_admin = true;
        $admin->save();
        auth()->logout();
        $this->post('/pengurus/login', ['email' => $admin->email, 'password' => 'testing-password-123'])->assertRedirect(route('creative.admin'));
        $this->get('/pengurus')->assertOk();
        $fields = ['kind' => 'product', 'name' => 'Produk admin', 'category' => 'Kriya', 'description' => 'Isi', 'published' => 1];
        $this->post('/pengurus/katalog', $fields)->assertSessionHasErrors('consent');
        $this->assertDatabaseMissing('creative_listings', ['name' => 'Produk admin']);
        $this->post('/pengurus/katalog', $fields + ['consent' => 1])->assertRedirect();
        $item = CreativeListing::firstOrFail();
        $this->assertTrue($item->published);
        $this->patch('/pengurus/katalog/'.$item->id, array_diff_key($fields, ['published' => 1]))->assertRedirect()->assertSessionHasNoErrors()->assertSessionHas('status', 'Data berhasil diperbarui.');
        $this->assertFalse($item->fresh()->published);
        $this->post('/pengurus/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
