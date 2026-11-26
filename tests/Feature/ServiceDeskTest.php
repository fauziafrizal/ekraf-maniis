<?php

namespace Tests\Feature;

use App\Models\EkrafServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceDeskTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_request_lifecycle_and_access_control(): void
    {
        $this->withoutVite();
        $this->get('/layanan-digital')->assertOk();
        $this->post(route('desk.submit'), ['category' => 'pembinaan'])->assertSessionHasErrors();
        $this->post(route('desk.submit'), [
            'category' => 'pembinaan',
            'applicant' => 'Pemohon Uji Privat',
            'contact' => 'kontak-uji@example.com',
            'description' => 'Deskripsi privat untuk pengujian.',
            'consent' => 1,
        ])->assertRedirect(route('desk.index'))->assertSessionHas('desk_receipt');
        $receipt = session('desk_receipt');
        $ticket = EkrafServiceRequest::sole();
        $this->assertSame(hash('sha256', $receipt['secret']), $ticket->secret_hash);
        $this->assertNotSame($receipt['secret'], $ticket->secret_hash);
        $this->get(route('desk.index'))->assertSee($receipt['reference'])->assertSee($receipt['secret']);
        $this->get(route('desk.index'))->assertDontSee($receipt['secret']);
        $this->post(route('desk.track'), ['reference' => $receipt['reference'], 'password' => str_repeat('0', 32)])->assertNotFound();
        $this->post(route('desk.track'), ['reference' => $receipt['reference'], 'password' => $receipt['secret']])->assertOk()->assertSee('Diterima')->assertDontSee('Pemohon Uji Privat')->assertDontSee('kontak-uji@example.com')->assertDontSee('Deskripsi privat');
        $this->get(route('desk.queue'))->assertRedirect(route('login'));
        $ordinary = User::factory()->create();
        $this->actingAs($ordinary)->get(route('desk.queue'))->assertForbidden();
        $this->patch(route('desk.update', $ticket), ['status' => 'reviewing', 'expected_status' => 'received'])->assertForbidden();
        $admin = User::factory()->create();
        $admin->is_ekraf_admin = true;
        $admin->save();
        $this->actingAs($admin)->get(route('desk.queue'))->assertOk()->assertSee('Pemohon Uji Privat');
        $this->patch(route('desk.update', $ticket), ['status' => 'completed', 'expected_status' => 'received'])->assertSessionHasErrors('status');
        $this->patch(route('desk.update', $ticket), ['status' => 'reviewing', 'expected_status' => 'received', 'internal_note' => 'Catatan privat khusus'])->assertRedirect(route('desk.queue'));
        $this->patch(route('desk.update', $ticket), ['status' => 'closed', 'expected_status' => 'received'])->assertSessionHasErrors('status');
        $this->assertSame('reviewing', $ticket->fresh()->status);
        $this->assertCount(2, $ticket->events);
        $this->post(route('desk.track'), ['reference' => $receipt['reference'], 'password' => $receipt['secret']])->assertOk()->assertSee('Dalam peninjauan')->assertDontSee('Catatan privat khusus')->assertDontSee('Pemohon Uji Privat');
    }
}
