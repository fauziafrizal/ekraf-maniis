<?php

namespace Tests\Feature;

use App\Models\CreativeListing;
use App\Models\EkrafCredential;
use App\Models\EkrafDocument;
use App\Models\EkrafPoll;
use App\Models\EkrafPost;
use App\Models\EkrafReview;
use App\Models\User;
use App\Support\EkrafRandomForest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EkrafExpansionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_ekraf_admin = true;
        $user->save();

        return $user;
    }

    public function test_public_modules_and_untrained_research_gate(): void
    {
        foreach (['/komunitas', '/komunitas?jenis=exhibition', '/komunitas?jenis=portfolio', '/komunitas?jenis=competition', '/komunitas?jenis=challenge', '/komunitas?jenis=collaboration', '/survei', '/peta-umkm', '/creative-challenge', '/notifikasi', '/studio-kreatif', '/klasifikasi-pembinaan'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/klasifikasi-pembinaan')->assertSee('Model penelitian belum tersedia');
        $this->post('/klasifikasi-pembinaan', [])->assertStatus(503);
        $this->post('/survei', ['rating' => 5, 'body' => 'Masukan uji'])->assertRedirect();
        $this->assertDatabaseHas('ekraf_feedback', ['rating' => 5]);
        $this->post('/survei', ['rating' => 6])->assertSessionHasErrors('rating');
    }

    public function test_moderation_and_private_uploads(): void
    {
        Storage::fake('local');
        $this->post('/komunitas', ['kind' => 'portfolio', 'title' => 'Karya Uji', 'display_name' => 'Kreator Uji', 'body' => 'Karya', 'consent' => 1, 'photo' => UploadedFile::fake()->image('karya.jpg', 200, 100)])->assertRedirect()->assertSessionHasNoErrors();
        $post = EkrafPost::firstOrFail();
        Storage::disk('local')->assertExists($post->photo_path);
        $this->get('/komunitas?jenis=portfolio')->assertDontSee('Karya Uji');
        $this->get('/komunitas/foto/'.$post->id)->assertNotFound();
        $this->actingAs(User::factory()->create())->post('/pengurus/sekretariat/moderasi/post/'.$post->id, ['published' => 1])->assertForbidden();
        $this->actingAs($this->admin())->get('/pengurus/sekretariat')->assertOk()->assertSee('Karya Uji');
        $this->post('/pengurus/sekretariat/moderasi/post/'.$post->id, ['published' => 1])->assertRedirect();
        $this->get('/komunitas?jenis=portfolio')->assertSee('Karya Uji');
        $this->get('/komunitas/foto/'.$post->id)->assertOk()->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_catalog_upload_qr_and_review(): void
    {
        Storage::fake('local');
        $this->actingAs($this->admin());
        $this->post('/pengurus/katalog', ['kind' => 'product', 'name' => 'Produk Uji', 'category' => 'Kriya', 'description' => 'Karya', 'published' => 1, 'consent' => 1, 'photo' => UploadedFile::fake()->image('foto.png', 200, 100)])->assertRedirect()->assertSessionHasNoErrors();
        $listing = CreativeListing::firstOrFail();
        $this->get('/katalog/'.$listing->id)->assertOk()->assertSee('Produk Uji');
        $this->get('/katalog/'.$listing->id.'/foto')->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $this->get('/katalog/'.$listing->id.'/qr')->assertOk()->assertHeader('Content-Type', 'image/svg+xml')->assertSee('<svg', false);
        $this->post('/katalog/'.$listing->id.'/ulasan', ['display_name' => 'Pelanggan', 'rating' => 4, 'body' => 'Ulasan Uji', 'consent' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $review = EkrafReview::firstOrFail();
        $this->get('/katalog/'.$listing->id)->assertDontSee('Ulasan Uji');
        $this->post('/pengurus/sekretariat/moderasi/review/'.$review->id, ['published' => 1])->assertRedirect();
        $this->get('/katalog/'.$listing->id)->assertSee('Ulasan Uji');
        $listing->update(['published' => false]);
        $this->get('/katalog/'.$listing->id.'/foto')->assertNotFound();
        $this->get('/katalog/'.$listing->id.'/qr')->assertNotFound();
    }

    public function test_office_credential_revocation_and_single_vote(): void
    {
        Storage::fake('local');
        $this->get('/pengurus/sekretariat')->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get('/pengurus/sekretariat')->assertForbidden();
        $admin = $this->admin();
        $this->actingAs($admin);
        $this->post('/pengurus/sekretariat/dokumen', ['kind' => 'notulen', 'number' => 'N-001', 'title' => 'Rapat Privat', 'document_date' => '2026-11-26', 'body' => 'Isi Privat', 'file' => UploadedFile::fake()->create('notulen.pdf', 10, 'application/pdf')])->assertRedirect()->assertSessionHasNoErrors();
        $doc = EkrafDocument::firstOrFail();
        $this->get('/pengurus/sekretariat/dokumen/'.$doc->id.'/cetak')->assertOk()->assertSee('Isi Privat');
        $this->get('/pengurus/sekretariat/dokumen/'.$doc->id.'/unduh')->assertOk();
        $this->post('/pengurus/sekretariat/terbitkan', ['kind' => 'certificate', 'number' => 'C-001', 'recipient' => 'Penerima Uji', 'title' => 'Pelatihan Uji', 'issued_on' => '2026-11-26', 'consent' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $cred = EkrafCredential::firstOrFail();
        $this->get('/verifikasi/'.$cred->token)->assertOk()->assertSee('Penerima Uji')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/verifikasi/'.$cred->token.'/qr')->assertOk()->assertHeader('Content-Type', 'image/svg+xml');
        $this->post('/pengurus/sekretariat/cabut/'.$cred->id)->assertRedirect();
        $this->get('/verifikasi/'.$cred->token)->assertNotFound();
        $this->post('/pengurus/sekretariat/voting', ['title' => 'Rapat Uji', 'options' => "Pilihan A\nPilihan B"])->assertRedirect()->assertSessionHasNoErrors();
        $poll = EkrafPoll::firstOrFail();
        $this->post('/pengurus/sekretariat/voting/'.$poll->id.'/pilih', ['choice' => 0])->assertRedirect();
        $this->post('/pengurus/sekretariat/voting/'.$poll->id.'/pilih', ['choice' => 1])->assertRedirect();
        $this->assertDatabaseCount('ekraf_votes', 1);
        $this->assertDatabaseHas('ekraf_votes', ['choice' => 0]);
        $this->get('/pengurus/sekretariat')->assertOk()->assertSee('Pilihan A');
        $this->post('/pengurus/sekretariat/voting/'.$poll->id.'/tutup')->assertRedirect();
        $this->post('/pengurus/sekretariat/voting/'.$poll->id.'/pilih', ['choice' => 0])->assertStatus(422);
        $this->actingAs(User::factory()->create())->get('/pengurus/sekretariat/dokumen/'.$doc->id.'/unduh')->assertForbidden();
    }

    public function test_ai_studio_fallback_and_optional_provider(): void
    {
        Http::fake(['https://api.openai.com/v1/responses' => Http::response(['output' => [['type' => 'message', 'content' => [['type' => 'output_text', 'text' => 'Draf uji AI']]]]])]);
        config(['services.ekraf_ai.enabled' => true, 'services.ekraf_ai.key' => 'test-only', 'services.ekraf_ai.model' => 'test-only']);
        $data = ['kind' => 'description', 'topic' => 'Keripik', 'facts' => 'Bahan singkong'];
        $this->post('/studio-kreatif', $data)->assertRedirect()->assertSessionHas('studio_mode', 'template');
        Http::assertNothingSent();
        $this->post('/studio-kreatif', $data + ['allow_ai' => 1])->assertRedirect()->assertSessionHas('studio_mode', 'ai')->assertSessionHas('studio_result', 'Draf uji AI');
    }

    public function test_forest_inference_with_explicit_test_fixture(): void
    {
        // Artificial test fixture only; never used as the deployed model.
        $forest = new EkrafRandomForest;
        $model = ['classes' => ['pemasaran', 'merek', 'keuangan', 'legalitas', 'produksi'], 'trees' => [['left' => [1, -1, -1], 'right' => [2, -1, -1], 'feature' => [0, -2, -2], 'threshold' => [3, -2, -2], 'value' => [[1, 1, 0, 0, 0], [0, 1, 0, 0, 0], [1, 0, 0, 0, 0]]]]];
        $this->assertSame('pemasaran', $forest->predict($model, [5, 1, 1, 1, 1])['label']);
        $this->assertSame('merek',$forest->predict($model,[1, 1, 1, 1, 1])['label']);
    }
}
