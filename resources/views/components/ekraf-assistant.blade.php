@php($staticAssistant = config('ekraf.static_preview', false))
<button class="em-assistant-launch" type="button" data-assistant-open hidden aria-haspopup="dialog"
    aria-label="Buka asisten Ekraf">
    <span class="em-assistant-symbol" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.6" stroke-linecap="round">
            <path
                d="M12 3v4m-2-2h4M5 8h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2ZM8 13h.01M16 13h.01M9 17h6" />
        </svg></span>
    <span class="em-assistant-launch-copy"><strong>Tanya Ekraf</strong><small>Asisten informasi</small></span>
</button>
<dialog class="em-assistant-dialog" data-assistant-dialog aria-labelledby="assistant-title"
    data-assistant-static="{{ $staticAssistant ? '1' : '0' }}"
    @unless($staticAssistant) data-endpoint="{{ route('creative.assistant') }}" @endunless
    data-ai-enabled="{{ ! $staticAssistant && config('services.ekraf_ai.enabled') && filled(config('services.ekraf_ai.key')) && filled(config('services.ekraf_ai.model')) ? '1' : '0' }}">
    <div class="em-assistant-head">
        <div class="em-assistant-identity">
            <span class="em-assistant-avatar" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"><path d="M12 3c0 5.5 3.5 9 9 9-5.5 0-9 3.5-9 9 0-5.5-3.5-9-9-9 5.5 0 9-3.5 9-9Z"/><path d="M19 3v4m-2-2h4"/></svg></span>
            <div><span class="em-assistant-eyebrow">Pemandu digital Ekraf Maniis</span><strong id="assistant-title">Sapa Ekraf</strong><span>Informasi dan panduan layanan</span></div>
        </div>
        <button type="button" data-assistant-close aria-label="Tutup asisten">×</button>
    </div>
    <div class="em-assistant-chat" data-assistant-chat role="log" aria-live="polite" aria-label="Percakapan">
        <p class="em-assistant-message">Sampurasun! Ada yang ingin Anda ketahui tentang Ekraf Maniis? Saya dapat
            membantu menemukan informasi program, pembinaan, produk lokal, dan potensi wilayah.</p>
    </div>
    <div class="em-assistant-compose">
    <details class="em-assistant-suggestions" open>
        <summary>Topik populer</summary>
        <div class="em-assistant-chips">
        @foreach (['Cara daftar UMKM', 'Jadwal program', 'Potensi Maniis', 'Pelatihan usaha'] as $question)
            <button type="button" data-assistant-question="{{ $question }}">{{ $question }}</button>
        @endforeach
    </div>
        </details>
    <div class="em-ai-tools"><button type="button" data-voice-listen hidden>Dikte suara</button><button type="button"
            data-voice-speak hidden>Bacakan jawaban</button><button type="button" data-voice-stop hidden>Hentikan
            suara</button><span data-voice-status role="status"></span></div>
    <details class="em-assistant-privacy"><summary>Informasi suara dan privasi</summary><p>Dikte suara memakai layanan browser jika didukung; suara dapat diproses oleh penyedia browser. Periksa teks sebelum mengirim.</p></details>
    <form class="em-assistant-form" method="post" data-assistant-form>
        @unless($staticAssistant) @csrf @endunless
        <label class="sr-only" for="assistant-question">Pertanyaan Anda</label>
        <input id="assistant-question" name="question" required minlength="2" maxlength="400" autocomplete="off"
            placeholder="Tanyakan sesuatu...">
        <button type="submit" aria-label="Kirim pertanyaan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4 20-7ZM22 2 11 13"/></svg><span>Kirim</span></button>
    </form>
    <label class="em-assistant-consent" data-assistant-consent hidden><input type="checkbox" data-assistant-ai> Gunakan
        AI eksternal (pertanyaan dikirim ke penyedia AI).</label>
    <p class="em-assistant-note" data-assistant-mode>{{ $staticAssistant ? 'Panduan otomatis berbasis informasi publik; pertanyaan diproses di browser, bukan AI eksternal.' : 'Mode pencarian informasi lokal. Jangan masukkan data pribadi.' }}</p>
    </div>
    @if ($staticAssistant)
        <template data-assistant-knowledge>
            @foreach (app(\App\Support\EkrafAssistant::class)->knowledge() as $entry)
                <div data-keywords="{{ $entry['keywords'] }}"><a href="{{ $entry['url'] }}">{{ $entry['title'] }}</a><p>{{ $entry['answer'] }}</p></div>
            @endforeach
        </template>
    @endif
</dialog>
