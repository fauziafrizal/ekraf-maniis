<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrowserSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_reload_with_security_headers(): void
    {
        $this->withoutVite();
        foreach (['/', '/profil', '/maniis', '/subsektor', '/program', '/kegiatan', '/hub', '/marketplace', '/direktori', '/akademi', '/ekraf-pelajar', '/data-center', '/peta', '/alat-usaha', '/ruang-saya', '/kalender', '/bantuan', '/layanan', '/kontak', '/komunitas', '/survei', '/peta-umkm', '/creative-challenge', '/notifikasi', '/studio-kreatif', '/klasifikasi-pembinaan', '/pengurus/login'] as $path) {
            for ($i = 0; $i < 2; $i++) {
                $this->get($path)->assertOk()
                    ->assertHeader('X-Frame-Options', 'DENY')
                    ->assertHeader('X-Content-Type-Options', 'nosniff')
                    ->assertHeader('Cache-Control', 'no-store, private')
                    ->assertHeader('Content-Security-Policy');
            }
        }
    }

    public function test_nonce_changes_and_matches_inline_script(): void
    {
        $this->withoutVite();
        $first = $this->get('/')->assertOk();
        $second = $this->get('/')->assertOk();
        preg_match("/'nonce-([^']+)'/", $first->headers->get('Content-Security-Policy'), $a);
        preg_match("/'nonce-([^']+)'/", $second->headers->get('Content-Security-Policy'), $b);
        $this->assertNotEmpty($a[1] ?? '');
        $this->assertNotEmpty($b[1] ?? '');
        $this->assertNotSame($a[1], $b[1]);
        $second->assertSee('nonce="'.$b[1].'"', false);
    }

    public function test_private_page_cache_and_role_checks(): void
    {
        $this->withoutVite();
        $this->get('/pengurus')->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get('/pengurus/sekretariat')->assertForbidden();
        $admin = User::factory()->create();
        $admin->is_ekraf_admin = true;
        $admin->save();
        $this->actingAs($admin)->get('/pengurus/sekretariat')->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertHeader('Referrer-Policy', 'no-referrer');
    }

    public function test_login_attempt_limit_prevents_subsequent_login(): void
    {
        $admin = User::factory()->create(['password' => 'test-password-2026']);
        $admin->is_ekraf_admin = true;
        $admin->save();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/pengurus/login', ['email' => $admin->email, 'password' => 'invalid-password'])->assertSessionHasErrors('email');
        }
        $this->post('/pengurus/login', ['email' => $admin->email, 'password' => 'test-password-2026'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
