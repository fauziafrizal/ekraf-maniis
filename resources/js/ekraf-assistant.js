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
    const staticMode = dialog.dataset.assistantStatic === '1';
    const knowledge = dialog.querySelector('[data-assistant-knowledge]');
    const stopWords = new Set('apa saya yang dan atau untuk dengan cara bagaimana bisa mau ingin ada itu di ke ekraf maniis tentang apakah tolong'.split(' '));
    const tokens = (value) => [...new Set((value.toLocaleLowerCase('id').match(/[\p{L}\p{N}]+/gu) || []).filter(word => word.length > 2 && !stopWords.has(word)))];
    function localAnswer(question) {
        const words = tokens(question);
        const entries = [...(knowledge?.content.querySelectorAll('[data-keywords]') || [])].map(row => {
            const link = row.querySelector('a');
            const answer = row.querySelector('p').textContent;
            const keys = tokens(row.dataset.keywords + ' ' + link.textContent);
            const body = tokens(answer);
            return {title:link.textContent, url:link.getAttribute('href'), answer, score:3 * words.filter(word=>keys.includes(word)).length + words.filter(word=>body.includes(word)).length};
        }).filter(entry=>entry.score>0).sort((a,b)=>b.score-a.score).slice(0,3);
        return {answer:entries[0]?.answer || 'Saya belum menemukan informasi yang sesuai. Coba tanyakan tentang UMKM, pelatihan, program, produk lokal, atau potensi Maniis. Untuk informasi resmi, hubungi pengurus melalui Pusat Layanan.',sources:entries,mode:'local'};
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
            let data;
            if (staticMode) {
                data = localAnswer(question);
            } else {
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
            data = await response.json();
            }
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
                    : staticMode ? 'Panduan otomatis berbasis informasi publik. Pertanyaan diproses di browser.' : 'Mode pencarian informasi lokal. Jangan masukkan data pribadi.';
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
