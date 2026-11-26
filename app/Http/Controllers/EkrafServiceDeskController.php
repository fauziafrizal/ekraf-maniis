<?php

namespace App\Http\Controllers;

use App\Models\EkrafServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EkrafServiceDeskController extends Controller
{
    public function index()
    {
        return $this->publicView();
    }

    private function publicView(array $data = [])
    {
        return response()->view('pages.service-desk.index', $data + [
            'pageTitle' => 'Layanan Digital & Pelacakan',
            'categories' => EkrafServiceRequest::CATEGORIES,
            'statuses' => EkrafServiceRequest::STATUSES,
        ])->header('Cache-Control', 'no-store, private')->header('X-Robots-Tag', 'noindex, nofollow')->header('Referrer-Policy', 'no-referrer');
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(array_keys(EkrafServiceRequest::CATEGORIES))],
            'applicant' => 'required|string|max:100',
            'contact' => 'nullable|string|max:150',
            'description' => 'required|string|min:10|max:2000',
            'consent' => 'accepted',
        ]);
        unset($data['consent']);
        $secret = bin2hex(random_bytes(16));
        $reference = 'EK-'.strtoupper(bin2hex(random_bytes(8)));

        DB::transaction(function () use ($data, $reference, $secret) {
            $ticket = EkrafServiceRequest::create($data + [
                'reference' => $reference,
                'secret_hash' => hash('sha256', $secret),
                'status' => 'received',
            ]);
            $ticket->events()->create(['status' => 'received']);
        });

        return redirect()->route('desk.index')->with('desk_receipt', ['reference' => $reference, 'secret' => $secret]);
    }

    public function track(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'regex:/^EK-[A-Fa-f0-9]{16}$/D'],
            'password' => ['required', 'string', 'regex:/^[A-Fa-f0-9]{32}$/D'],
        ]);
        $ticket = EkrafServiceRequest::where('reference', strtoupper($data['reference']))->first();
        abort_unless($ticket && hash_equals($ticket->secret_hash, hash('sha256', strtolower($data['password']))), 404);

        // Never pass the applicant, contact, description or private notes to the tracking view.
        return $this->publicView([
            'tracking' => ['reference' => $ticket->reference, 'status' => $ticket->status, 'category' => $ticket->category],
            'timeline' => $ticket->events()->orderBy('id')->get(['status', 'created_at']),
        ]);
    }

    private function admin(): void
    {
        abort_unless(auth()->user()?->is_ekraf_admin, 403);
    }

    public function queue(Request $request)
    {
        $this->admin();
        $status = $request->validate(['status' => ['nullable', Rule::in(array_keys(EkrafServiceRequest::STATUSES))]])['status'] ?? null;
        $counts = EkrafServiceRequest::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $tickets = EkrafServiceRequest::query()->with('events')->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(20)->withQueryString();

        return response()->view('pages.service-desk.queue', [
            'pageTitle' => 'Antrean Layanan Pengurus',
            'tickets' => $tickets,
            'counts' => $counts,
            'status' => $status,
            'statuses' => EkrafServiceRequest::STATUSES,
            'categories' => EkrafServiceRequest::CATEGORIES,
        ])->header('Cache-Control', 'no-store, private');
    }

    public function update(Request $request, EkrafServiceRequest $ticket)
    {
        $this->admin();
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(EkrafServiceRequest::STATUSES))],
            'expected_status' => ['required', Rule::in(array_keys(EkrafServiceRequest::STATUSES))],
            'internal_note' => 'nullable|string|max:1500',
        ]);
        DB::transaction(function () use ($ticket, $data) {
            $fresh = EkrafServiceRequest::query()->lockForUpdate()->findOrFail($ticket->id);
            if ($fresh->status !== $data['expected_status'] || ! in_array($data['status'], EkrafServiceRequest::TRANSITIONS[$fresh->status], true)) {
                throw ValidationException::withMessages(['status' => 'Status telah berubah atau transisi tidak diizinkan. Muat ulang antrean.']);
            }
            // Conditional update also guards against stale concurrent writes.
            $updated = EkrafServiceRequest::whereKey($fresh->id)->where('status', $data['expected_status'])->update(['status' => $data['status']]);
            abort_unless($updated === 1, 409);
            $fresh->events()->create(['status' => $data['status'], 'user_id' => auth()->id(), 'internal_note' => $data['internal_note'] ?? null]);
        });

        return redirect()->route('desk.queue')->with('status', 'Status pengajuan dan riwayat berhasil diperbarui.');
    }
}
