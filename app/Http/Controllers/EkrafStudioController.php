<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class EkrafStudioController extends Controller
{
    public function index()
    {
        return view('pages.community.studio', ['pageTitle' => 'AI Creative Studio']);
    }

    public function generate(Request $request)
    {
        $data = $request->validate(['kind' => 'required|in:idea,description', 'topic' => 'required|string|max:100', 'facts' => 'required|string|max:600', 'allow_ai' => 'sometimes|boolean']);
        $local = $data['kind'] === 'idea' ? 'Ide eksplorasi: '.$data['topic']."\nMulai dari potensi yang Anda sebutkan: ".$data['facts']."\n1. Buat contoh produk atau layanan kecil.\n2. Wawancarai calon pelanggan.\n3. Catat biaya dan uji minat pasar.\n4. Tinjau izin dan risiko sebelum menjual." : $data['topic']."\n\n".$data['facts']."\n\nHubungi penjual untuk informasi ketersediaan dan pemesanan.";
        $mode = 'template';
        $key = 'ekraf-ai-budget:'.now()->format('Y-m-d');
        if ($request->boolean('allow_ai') && config('services.ekraf_ai.enabled') && filled(config('services.ekraf_ai.key')) && filled(config('services.ekraf_ai.model')) && ! RateLimiter::tooManyAttempts($key, max(1, min(500, (int) config('services.ekraf_ai.daily_limit', 50))))) {
            RateLimiter::hit($key, 86400);
            try {
                $result = Http::withToken(config('services.ekraf_ai.key'))->acceptJson()->timeout(12)->post('https://api.openai.com/v1/responses', ['model' => config('services.ekraf_ai.model'), 'store' => false, 'max_output_tokens' => 450, 'instructions' => 'Bantu membuat draf ide usaha kreatif atau deskripsi produk dalam bahasa Indonesia. Jangan mengarang klaim produk, harga, jaminan pendapatan, testimoni, atau legalitas. Ide adalah hipotesis untuk divalidasi, bukan janji hasil. Jangan meminta data pribadi.', 'input' => json_encode(['task' => $data['kind'], 'topic' => $data['topic'], 'facts' => $data['facts']], JSON_UNESCAPED_UNICODE)]);
                if ($result->successful()) {
                    $text = collect($result->json('output', []))->where('type', 'message')->flatMap(fn ($x) => $x['content'] ?? [])->where('type', 'output_text')->pluck('text')->implode("\n");
                    if (filled($text)) {
                        $local = Str::limit($text, 3000);
                        $mode = 'ai';
                    }
                }
            } catch (\Throwable) {
            }
        }

        return back()->with('studio_result', $local)->with('studio_mode', $mode)->withInput($request->only('kind','topic','facts'));
    }
}
