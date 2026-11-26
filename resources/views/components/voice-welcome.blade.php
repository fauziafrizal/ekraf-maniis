<section class="em-voice-welcome" data-voice-welcome aria-labelledby="voice-welcome-title">
    <div class="site-shell em-voice-inner">
        <div class="em-voice-orb" aria-hidden="true"><span></span><span></span><svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                <rect x="9" y="2" width="6" height="13" rx="3" />
                <path d="M5 10v2a7 7 0 0 0 14 0v-2M12 19v3M9 22h6" />
            </svg></div>
        <div class="em-voice-content">
            <p class="em-kicker">Sapa Ekraf · Pemandu suara virtual</p>
            <h2 id="voice-welcome-title">Sampurasun, selamat datang di Maniis!</h2>
            <p>Kenali website Ekraf Maniis melalui sambutan suara berbahasa Indonesia.</p>
            @php($narration = config('ekraf.welcome_narration'))
            @php($audioFile = is_file(public_path('audio/sapa-ekraf-welcome.mp3')) ? 'audio/sapa-ekraf-welcome.mp3' : (is_file(public_path('audio/sapa-ekraf-welcome.m4a')) ? 'audio/sapa-ekraf-welcome.m4a' : null))
            @if ($audioFile)
                <audio data-welcome-audio preload="metadata" playsinline src="{{ asset($audioFile) }}" hidden
                    aria-label="Sambutan suara AI"></audio>
            @endif
            <div class="em-voice-actions" data-welcome-controls>
                <button class="em-voice-play" type="button" data-welcome-play disabled><svg viewBox="0 0 24 24"
                        fill="currentColor" aria-hidden="true">
                        <path d="M8 5v14l12-7z" />
                    </svg><span>Dengarkan sambutan AI</span></button>
                <button class="em-voice-stop" type="button" data-welcome-stop hidden>Hentikan</button>
                <span class="em-voice-wave" aria-hidden="true">
                    @for ($i = 0; $i < 9; $i++)
                        <i style="--i:{{ $i }}"></i>
                    @endfor
                </span>
            </div>
            <p data-welcome-status role="status" class="em-voice-status">Narasi disintesis, bukan rekaman manusia.</p>
            <details class="em-voice-transcript">
                <summary>Baca transkrip sambutan</summary>
                <p data-welcome-text>{{ $narration }}</p>
            </details>
        </div>
    </div>
</section>
