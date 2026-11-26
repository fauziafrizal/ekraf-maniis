<?php

namespace App\Http\Controllers;

use App\Models\EkrafCredential;
use App\Models\EkrafDocument;
use App\Models\EkrafFeedback;
use App\Models\EkrafPoll;
use App\Models\EkrafPost;
use App\Models\EkrafReview;
use App\Models\EkrafVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EkrafOfficeController extends Controller
{
    private function admin(): void
    {
        abort_unless(auth()->user()?->is_ekraf_admin, 403);
    }

    public function index()
    {
        $this->admin();

        return response()->view('pages.office.index', ['pageTitle' => 'Sekretariat Digital', 'documents' => EkrafDocument::latest()->paginate(15, ['*'], 'dokumen'), 'credentials' => EkrafCredential::latest()->paginate(15, ['*'], 'kartu'), 'posts' => EkrafPost::latest()->paginate(15, ['*'], 'kiriman'), 'reviews' => EkrafReview::latest()->paginate(15, ['*'], 'ulasan'), 'feedback' => EkrafFeedback::latest()->paginate(15, ['*'], 'survei'), 'polls' => EkrafPoll::latest()->paginate(15, ['*'], 'voting')])->header('Cache-Control', 'no-store, private');
    }

    public function document(Request $request)
    {
        $this->admin();
        $data = $request->validate(['kind' => 'required|in:surat_masuk,surat_keluar,arsip,notulen', 'number' => ['required', 'string', 'max:100', Rule::unique('ekraf_documents')->where('kind', $request->input('kind'))], 'title' => 'required|string|max:200', 'document_date' => 'required|date', 'body' => 'required|string|max:15000', 'file' => 'nullable|file|mimes:pdf|max:10240']);
        unset($data['file']);
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('ekraf-office', 'local');
            abort_unless($path, 500);
            $data['file_path'] = $path;
        }
        EkrafDocument::create($data);

        return back()->with('status', 'Dokumen disimpan secara privat.');
    }

    public function download(EkrafDocument $document)
    {
        $this->admin();
        abort_unless($document->file_path && Storage::disk('local')->exists($document->file_path), 404);

        return Storage::disk('local')->download($document->file_path, 'dokumen-'.$document->id.'.pdf', ['Cache-Control' => 'no-store, private']);
    }

    public function printDocument(EkrafDocument $document)
    {
        $this->admin();

        return response()->view('pages.office.print-document', ['document' => $document])->header('Cache-Control', 'no-store');
    }

    public function issue(Request $request)
    {
        $this->admin();
        $data = $request->validate(['kind' => 'required|in:certificate,member', 'number' => 'required|string|max:100|unique:ekraf_credentials,number', 'recipient' => 'required|string|max:150', 'title' => 'required|string|max:200', 'issued_on' => 'required|date', 'consent' => 'accepted']);
        unset($data['consent']);
        EkrafCredential::create($data + ['token' => (string) Str::uuid(), 'active' => true]);

        return back()->with('status', 'Dokumen verifikasi diterbitkan.');
    }

    public function revoke(EkrafCredential $credential)
    {
        $this->admin();
        $credential->update(['active' => false]);

        return back()->with('status', 'Dokumen dicabut.');
    }

    public function preview(EkrafPost $post)
    {
        $this->admin();
        abort_unless($post->photo_path && Storage::disk('local')->exists($post->photo_path), 404);

        return Storage::disk('local')->response($post->photo_path, null, ['Content-Type' => 'image/jpeg', 'Cache-Control' => 'no-store']);
    }

    public function moderate(Request $request, string $kind, int $id)
    {
        $this->admin();
        $data = $request->validate(['published' => 'required|boolean']);
        $model = match ($kind) {
            'post' => EkrafPost::class,'review' => EkrafReview::class,default => abort(404)
        };
        $model::findOrFail($id)->update(['published' => $data['published']]);

        return back()->with('status', 'Status publikasi diperbarui.');
    }

    public function poll(Request $request)
    {
        $this->admin();
        $data = $request->validate(['title' => 'required|string|max:200', 'options' => 'required|string|max:1000']);
        $options = array_values(array_unique(array_filter(array_map('trim', preg_split('/\R/u', $data['options'])))));
        if (count($options) < 2 || count($options) > 10 || collect($options)->contains(fn ($o) => mb_strlen($o) > 100)) {
            throw ValidationException::withMessages(['options' => 'Masukkan 2–10 pilihan berbeda, maksimal 100 karakter per pilihan.']);
        }
        EkrafPoll::create(['title' => $data['title'], 'options' => $options, 'open' => true]);

        return back()->with('status', 'Voting internal dibuka.');
    }

    public function vote(Request $request, EkrafPoll $poll)
    {
        $this->admin();
        $data = $request->validate(['choice' => 'required|integer|min:0']);
        DB::transaction(function () use ($poll, $data) {
            $fresh = EkrafPoll::query()->lockForUpdate()->findOrFail($poll->id);
            abort_unless($fresh->open && array_key_exists($data['choice'], $fresh->options), 422);
            EkrafVote::firstOrCreate(['ekraf_poll_id' => $fresh->id, 'user_id' => auth()->id()], ['choice' => $data['choice']]);
        });

        return back()->with('status', 'Pilihan pertama Anda tercatat; satu suara per akun.');
    }

    public function close(EkrafPoll $poll)
    {
        $this->admin();
        $poll->update(['open' => false]);

        return back()->with('status', 'Voting ditutup.');
    }
}
