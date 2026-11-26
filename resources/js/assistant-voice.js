const dialog = document.querySelector('[data-assistant-dialog]');
if (dialog) {
    const input = dialog.querySelector('[name=question]'),
        chat = dialog.querySelector('[data-assistant-chat]'),
        listen = dialog.querySelector('[data-voice-listen]'),
        speak = dialog.querySelector('[data-voice-speak]'),
        stop = dialog.querySelector('[data-voice-stop]'),
        status = dialog.querySelector('[data-voice-status]');
    const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition;
    if (Recognition) {
        listen.hidden = false;
        listen.addEventListener('click', () => {
            if (recognition) {
                recognition.stop();
                return;
            }
            try {
                recognition = new Recognition();
                recognition.lang = 'id-ID';
                recognition.interimResults = false;
                recognition.onresult = (e) => {
                    input.value = e.results[0][0].transcript.slice(0, 400);
                    status.textContent = 'Periksa teks sebelum mengirim.';
                };
                recognition.onerror = () => (status.textContent = 'Pengenalan suara tidak tersedia. Ketik pertanyaan.');
                recognition.onend = () => {
                    recognition = null;
                    listen.textContent = 'Dikte suara';
                };
                recognition.start();
                listen.textContent = 'Hentikan dikte';
                status.textContent = 'Mendengarkan…';
            } catch {
                recognition = null;
                status.textContent = 'Mikrofon tidak tersedia.';
            }
        });
    }
    if ('speechSynthesis' in window) {
        speak.hidden = false;
        stop.hidden = false;
        speak.addEventListener('click', () => {
            const last = [...chat.querySelectorAll('.em-assistant-message:not(.em-assistant-user)')].at(-1);
            if (!last) return;
            window.speechSynthesis.cancel();
            const u = new SpeechSynthesisUtterance(last.textContent);
            u.lang = 'id-ID';
            window.speechSynthesis.speak(u);
        });
        stop.addEventListener('click', () => window.speechSynthesis.cancel());
    }
    dialog.addEventListener('close', () => {
        recognition?.stop();
        window.speechSynthesis?.cancel();
    });
}
