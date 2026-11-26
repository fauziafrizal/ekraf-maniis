<?php

namespace Tests\Feature;

use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EkrafAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_assistant_answers_and_handles_unknown_questions(): void
    {
        config(['services.ekraf_ai.enabled' => false]);
        Http::fake();
        $this->postJson('/asisten-ekraf', ['question' => 'Cara daftar UMKM'])->assertOk()->assertJsonPath('mode', 'local')->assertJsonPath('sources.0.title', 'Pendataan usaha');
        $this->postJson('/asisten-ekraf', ['question' => 'zzyzxzz'])->assertOk()->assertJsonPath('mode', 'local')->assertJsonPath('sources.0.title', 'Pusat Layanan');
        $this->postJson('/asisten-ekraf', ['question' => str_repeat('a', 401)])->assertUnprocessable();
        Http::assertNothingSent();
    }

    public function test_publication_filter_and_explicit_external_consent(): void
    {
        Program::create(['title' => 'Pelatihan Zafira', 'slug' => 'zafira', 'summary' => 'Kelas Zafira', 'body' => 'Isi', 'audience' => 'Warga', 'published_at' => now()->subDay()]);
        Program::create(['title' => 'Rahasia Qwerty', 'slug' => 'qwerty', 'summary' => 'Rahasia Qwerty', 'body' => 'Isi', 'audience' => 'Warga', 'published_at' => null]);
        config(['services.ekraf_ai.enabled' => true, 'services.ekraf_ai.key' => 'test-key', 'services.ekraf_ai.model' => 'test-model']);
        Http::fake(['https://api.openai.com/v1/responses' => Http::response(['output' => [['type' => 'message', 'content' => [['type' => 'output_text', 'text' => 'Jawaban AI uji.']]]]])]);
        $this->postJson('/asisten-ekraf', ['question' => 'Zafira'])->assertOk()->assertJsonPath('sources.0.title', 'Pelatihan Zafira')->assertJsonPath('mode', 'local');
        $this->postJson('/asisten-ekraf', ['question' => 'Qwerty'])->assertOk()->assertJsonPath('sources.0.title', 'Pusat Layanan');
        Http::assertNothingSent();
        $this->postJson('/asisten-ekraf', ['question' => 'Zafira', 'allow_ai' => true])->assertOk()->assertJsonPath('mode', 'ai')->assertJsonPath('answer', 'Jawaban AI uji.');
        Http::assertSent(fn ($request) => $request->url() === 'https://api.openai.com/v1/responses' && $request['store'] === false && ! str_contains($request['input'],'Qwerty'));
    }
}
