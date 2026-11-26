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
    let active = false,
        resumeMusic = false,
        originalVolume = 0.2,
        utterance,
        generation = 0;
    const render = (playing, message) => {
        active = playing;
        section.toggleAttribute('data-welcome-playing', playing);
        play.querySelector('span').textContent = playing ? 'Sambutan sedang diputar' : 'Dengarkan sambutan AI';
        play.disabled = playing;
        stop.hidden = !playing;
        status.textContent = message;
    };
    const finish = (message, restore = true) => {
        if (!active) return;
        generation++;
        render(false, message);
        if (music) {
            music.volume = originalVolume;
            if (restore && resumeMusic && music.paused) music.play().catch(() => {});
        }
        resumeMusic = false;
    };
    const begin = () => {
        originalVolume = music?.volume ?? 0.2;
        resumeMusic = !!music && !music.paused;
        if (music) {
            music.volume = 0;
            music.pause();
        }
        synth?.cancel();
        render(true, 'Sambutan sedang diputar…');
    };
    if (audio || synth) {
        controls.hidden = false;
        play.disabled = false;
    } else {
        status.textContent = 'Suara tidak tersedia. Silakan baca transkrip.';
    }
    if (audio) audio.controls = false;
    if (synth) {
        // Some devices expose higher-quality voices only after this list is loaded.
        synth.getVoices();
    }
    play.addEventListener('click', () => {
        if (active) return;
        if (!audio) {
            const voices = synth.getVoices().filter((v) => /^id(?:[-_]|$)/i.test(v.lang));
            const rank = (v) =>
                (/premium|enhanced|natural|neural/i.test(v.name) ? 10 : /google|microsoft/i.test(v.name) ? 5 : 0) +
                (v.localService ? 1 : 0);
            const voice = voices.sort((a, b) => rank(b) - rank(a))[0];
            if (!voice) {
                status.textContent = 'Suara Bahasa Indonesia tidak tersedia pada browser ini. Silakan baca transkrip.';
                return;
            }
            begin();
            const current = ++generation;
            utterance = new SpeechSynthesisUtterance(text);
            utterance.voice = voice;
            utterance.lang = voice.lang;
            utterance.rate = 0.93;
            utterance.pitch = 1;
            utterance.onend = () => {
                if (current === generation) finish('Sambutan selesai. Selamat menjelajahi website!');
            };
            utterance.onerror = () => {
                if (current === generation) finish('Suara belum dapat diputar. Coba lagi atau baca transkrip.');
            };
            synth.speak(utterance);
            return;
        }
        begin();
        audio.currentTime = 0;
        audio.play().catch(() => finish('Audio belum dapat diputar. Coba lagi.'));
    });
    if (audio) {
        audio.addEventListener('ended', () => finish('Sambutan selesai. Selamat menjelajahi website!'));
        audio.addEventListener('error', () => finish('Audio belum dapat dimuat. Baca transkrip sambutan.'));
    }
    const cancel = (message, restore = true) => {
        if (!active) return;
        finish(message, restore);
        audio?.pause();
        synth?.cancel();
    };
    stop.addEventListener('click', () => cancel('Sambutan dihentikan.'));
    musicToggle?.addEventListener('click', () => cancel('Sambutan dihentikan karena Anda memilih musik.', false), {
        capture: true,
    });
    // Opening the assistant stops the welcome so two narrations never overlap.
    document.querySelector('[data-assistant-open]')?.addEventListener('click', () => cancel('Sambutan dihentikan.'));
    window.addEventListener('pagehide', () => cancel('Sambutan dihentikan.', false));
}
