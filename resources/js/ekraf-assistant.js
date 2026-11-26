const dialog = document.querySelector('[data-assistant-dialog]');
const launch = document.querySelector('[data-assistant-open]');

if (dialog?.showModal && launch) {
    const form = dialog.querySelector('[data-assistant-form]');
    const input = form.elements.question;
    const chat = dialog.querySelector('[data-assistant-chat]');
    const submit = form.querySelector('button[type="submit"]');
    const consent = dialog.querySelector('[data-assistant-consent]');
    const ai = dialog.querySelector('[data-assistant-ai]');
    const mode = dialog.querySelector('[data-assistant-mode]');
    let busy = false;
    launch.hidden = false;
    consent.hidden = dialog.dataset.aiEnabled !== '1';

    function message(text, user = false) {
        const p = document.createElement('p');
        p.className = 'em-assistant-message' + (user ? ' em-assistant-user' : '');
        p.textContent = text;
        chat.append(p);
        chat.scrollTop = chat.scrollHeight;
        return p;
    }
    async function ask(question) {
        if (busy || question.trim().length < 2) return;
        busy = true;
        submit.disabled = true;
        message(question, true);
        const suggestions = dialog.querySelector('.em-assistant-suggestions');
        if (suggestions) suggestions.open = false;
        input.value = '';
        const waiting = message('Mencari informasi…');
        waiting.classList.add('em-assistant-waiting');
        submit.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(dialog.dataset.endpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.elements._token.value,
                },
                body: JSON.stringify({ question, allow_ai: ai.checked && !consent.hidden }),
            });
            if (response.status === 419 || response.status === 403) {
                waiting.textContent = 'Sesi telah berakhir. Muat ulang halaman sebelum bertanya kembali.';
                return;
            }
            if (response.status === 429) {
                waiting.textContent = 'Terlalu banyak pertanyaan. Coba kembali beberapa saat lagi.';
                return;
            }
            if (!response.ok) throw Error('request');
            const data = await response.json();
            waiting.textContent = data.answer;
            for (const source of data.sources || []) {
                const url = new URL(source.url, location.origin);
                if (url.origin !== location.origin) continue;
                const a = document.createElement('a');
                a.href = url.href;
                a.textContent = source.title;
                a.className = 'em-assistant-source';
                waiting.append(a);
            }
            mode.textContent =
                data.mode === 'ai'
                    ? 'Jawaban AI berdasarkan informasi publik. Periksa sumber sebelum bertindak.'
                    : 'Mode pencarian informasi lokal. Jangan masukkan data pribadi.';
        } catch {
            waiting.textContent = 'Asisten belum dapat terhubung. Silakan coba lagi atau buka Pusat Layanan.';
        } finally {
            busy = false;
            waiting.classList.remove('em-assistant-waiting');
            submit.removeAttribute('aria-busy');
            submit.disabled = false;
            chat.scrollTop = chat.scrollHeight;
            if (dialog.open) input.focus({ preventScroll: true });
        }
    }
    launch.addEventListener('click', () => {
        dialog.showModal();
        input.focus();
    });
    dialog.querySelector('[data-assistant-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => launch.focus({ preventScroll: true }));
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        ask(input.value.trim());
    });
    dialog
        .querySelectorAll('[data-assistant-question]')
        .forEach((button) => button.addEventListener('click', () => ask(button.dataset.assistantQuestion)));
}
