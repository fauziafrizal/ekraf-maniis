const section = document.querySelector('[data-voice-welcome]');
if (section) {
    const audio = section.querySelector('[data-welcome-audio]');
    const controls = section.querySelector('[data-welcome-controls]');
    const play = section.querySelector('[data-welcome-play]');
    const stop = section.querySelector('[data-welcome-stop]');
    const status = section.querySelector('[data-welcome-status]');
    const text = section.querySelector('[data-welcome-text]').textContent.trim();
    const music = document.querySelector('[data-site-music]');
    const musicToggle = document.querySelector('[data-music-toggle]');
    const synth = window.speechSynthesis;
    const label = play.querySelector('span');
    let active = false, resumeMusic = false, utterance, generation = 0, watchdog;
    let voices = [];
    const refreshVoices = () => { voices = synth?.getVoices() || []; };
    refreshVoices();
    synth?.addEventListener?.('voiceschanged', refreshVoices);
    window.addEventListener('pageshow', refreshVoices);
    const render = (state, message) => {
        section.toggleAttribute('data-welcome-active', state !== 'idle');
        section.toggleAttribute('data-welcome-playing', state === 'playing');
        section.toggleAttribute('data-welcome-loading', state === 'loading');
        label.textContent = state === 'playing' ? 'Sambutan sedang diputar' : state === 'loading' ? 'Menyiapkan suara…' : 'Dengarkan sambutan AI';
        play.disabled = state !== 'idle';
        stop.hidden = state === 'idle';
        status.textContent = message;
    };
    const finish = (message, restore = true, halt = false) => {
        if (!active) return;
        active = false;
        generation++;
        clearTimeout(watchdog);
        if (halt) {
            audio?.pause();
            synth?.cancel();
        }
        utterance = undefined;
        render('idle', message);
        if (music && restore && resumeMusic && music.paused) music.play().catch(() => {});
        resumeMusic = false;
    };
    const loading = (message) => {
        if (!active) return;
        render('loading', message);
        clearTimeout(watchdog);
        const current = generation;
        watchdog = setTimeout(() => {
            if (active && current === generation) finish('Suara belum siap. Coba lagi atau baca transkrip.', true, true);
        }, 18000);
    };
    const playing = () => {
        if (!active) return;
        clearTimeout(watchdog);
        render('playing', 'Sambutan sedang diputar…');
    };
    const begin = () => {
        active = true;
        generation++;
        resumeMusic = !!music && !music.paused;
        music?.pause();
        loading('Menyiapkan sambutan suara…');
        return generation;
    };
    if (audio || synth) {
        controls.hidden = false;
        play.disabled = false;
    } else {
        status.textContent = 'Suara tidak tersedia. Silakan baca transkrip.';
    }
    if (audio) audio.controls = false;
    play.addEventListener('click', () => {
        if (active) return;
        if (audio) {
            const current = begin();
            try {
                audio.currentTime = 0;
                // Called directly within the user gesture, including iOS Safari.
                audio.play().catch(() => {
                    if (current === generation) finish('Audio belum dapat diputar. Coba lagi.', true, true);
                });
            } catch {
                finish('Audio belum dapat diputar. Coba lagi.', true, true);
            }
            return;
        }
        if (!synth) return;
        refreshVoices();
        const candidates = voices.filter(v => /^id(?:[-_]|$)/i.test(v.lang));
        const rank = v => (v.localService ? 20 : 0) + (/premium|enhanced|natural|neural/i.test(v.name) ? 10 : 0);
        const voice = candidates.sort((a, b) => rank(b) - rank(a))[0];
        if (!voice && voices.length) {
            status.textContent = 'Suara Bahasa Indonesia belum tersedia pada perangkat. Aktifkan suara Indonesia di pengaturan perangkat atau baca transkrip.';
            return;
        }
        if (synth.speaking || synth.pending) synth.cancel();
        const current = begin();
        utterance = new SpeechSynthesisUtterance(text);
        if (voice) utterance.voice = voice;
        utterance.lang = voice?.lang || 'id-ID';
        utterance.rate = 0.96;
        utterance.pitch = 1;
        const valid = () => active && current === generation;
        utterance.onstart = () => { if (valid()) playing(); };
        utterance.onboundary = () => {
            if (valid() && !section.hasAttribute('data-welcome-playing') && !synth.paused) playing();
        };
        utterance.onpause = () => { if (valid()) loading('Suara dijeda…'); };
        utterance.onresume = () => { if (valid()) playing(); };
        utterance.onend = () => { if (valid()) finish('Sambutan selesai. Selamat menjelajahi website!'); };
        utterance.onerror = () => { if (valid()) finish('Suara belum dapat diputar. Coba lagi atau baca transkrip.'); };
        try {
            synth.speak(utterance);
        } catch {
            finish('Suara belum dapat diputar. Coba lagi atau baca transkrip.', true, true);
        }
    });
    if (audio) {
        audio.addEventListener('playing', playing);
        audio.addEventListener('waiting', () => loading('Memuat suara…'));
        audio.addEventListener('stalled', () => { if (audio.readyState < 3) loading('Memuat suara…'); });
        audio.addEventListener('pause', () => {
            if (active && !audio.ended) loading('Suara dijeda…');
        });
        audio.addEventListener('ended', () => finish('Sambutan selesai. Selamat menjelajahi website!'));
        audio.addEventListener('error', () => finish('Audio belum dapat dimuat. Baca transkrip sambutan.', true, true));
    }
    const cancel = (message, restore = true) => finish(message, restore, true);
    stop.addEventListener('click', () => cancel('Sambutan dihentikan.'));
    musicToggle?.addEventListener('click', () => cancel('Sambutan dihentikan karena Anda memilih musik.', false), {capture: true});
    document.querySelector('[data-assistant-open]')?.addEventListener('click', () => cancel('Sambutan dihentikan.'));
    window.addEventListener('pagehide', () => cancel('Sambutan dihentikan.', false));
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) cancel('Sambutan dihentikan saat halaman tidak aktif.', false);
    });
}
