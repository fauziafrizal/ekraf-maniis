const money = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
function show(form, text) {
    const out = form.querySelector('[data-tool-output]');
    out.textContent = text;
    out.hidden = false;
}
function bindForm(selector, fn) {
    const form = document.querySelector(selector);
    if (!form) return;
    form.hidden = false;
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        fn(form, Object.fromEntries(new FormData(form)));
    });
}
bindForm('[data-tool-bep]', (form, v) => {
    const fixed = Number(v.fixed),
        margin = Number(v.price) - Number(v.variable);
    show(
        form,
        margin <= 0
            ? 'Harga jual harus lebih besar dari biaya variabel per unit agar titik impas dapat dicapai.'
            : 'Titik impas: ' +
                  Math.ceil(fixed / margin).toLocaleString('id-ID') +
                  ' unit. Kontribusi per unit: ' +
                  money.format(margin) +
                  '.'
    );
});
bindForm('[data-tool-profit]', (form, v) => {
    const revenue = Number(v.quantity) * Number(v.price);
    const cost = Number(v.quantity) * Number(v.variable) + Number(v.fixed);
    const profit = revenue - cost;
    show(
        form,
        'Pendapatan: ' +
            money.format(revenue) +
            ' · Total biaya: ' +
            money.format(cost) +
            ' · ' +
            (profit < 0 ? 'Estimasi rugi: ' : 'Estimasi laba: ') +
            money.format(Math.abs(profit)) +
            '.'
    );
});
bindForm('[data-tool-recommend]', (form, v) => {
    const steps = {
        idea: 'Mulai dengan validasi ide dan calon pelanggan.',
        start: 'Perkuat pencatatan usaha dan konsistensi kualitas.',
        grow: 'Evaluasi kapasitas, pasar, dan peluang kemitraan.',
    };
    const advice = {
        marketing: 'Pelajari pemasaran digital di Ekraf Academy.',
        brand: 'Latih foto produk dan identitas merek.',
        finance: 'Hitung biaya, harga jual, dan titik impas.',
        legal: 'Tanyakan informasi legalitas kepada pengurus atau kanal resmi pemerintah.',
        skills: 'Ajukan pembinaan keterampilan yang sesuai dengan usaha.',
    };
    const needs = new FormData(form).getAll('needs');
    show(form, [steps[v.stage], ...needs.map((n) => advice[n])].join('\n'));
});
const caption = document.querySelector('[data-tool-caption]');
bindForm('[data-tool-caption]', (form, v) => {
    const output = form.querySelector('[data-tool-caption-output]');
    output.value = v.product.trim() + '\n\n' + v.benefit.trim() + '\n\n' + v.cta + '\n\n#EkrafManiis #ProdukLokal';
    output.hidden = false;
    form.querySelector('[data-tool-copy]').hidden = false;
});
caption?.querySelector('[data-tool-copy]').addEventListener('click', async () => {
    const output = caption.querySelector('[data-tool-caption-output]');
    try {
        await navigator.clipboard.writeText(output.value);
        caption.querySelector('[data-tool-copy-status]').textContent = 'Caption disalin.';
    } catch {
        output.focus();
        output.select();
        caption.querySelector('[data-tool-copy-status]').textContent = 'Pilih dan salin teks secara manual.';
    }
});
function read(key) {
    try {
        return localStorage.getItem(key) === '1';
    } catch {
        return false;
    }
}
function write(key, value) {
    try {
        localStorage.setItem(key, value ? '1' : '0');
    } catch {}
}
const checklist = document.querySelector('[data-tool-checklist]');
if (checklist) {
    const tasks = [...checklist.querySelectorAll('[data-tool-task]')];
    function render() {
        const total = tasks.filter((t) => t.checked).length;
        checklist.querySelector('progress').value = total;
        checklist.querySelector('[data-tool-progress-label]').textContent =
            total + ' dari ' + tasks.length + ' langkah selesai';
    }
    tasks.forEach((t) => {
        t.checked = read('ekraf-ready-' + t.dataset.toolTask);
        t.addEventListener('change', () => {
            write('ekraf-ready-' + t.dataset.toolTask, t.checked);
            render();
        });
    });
    checklist.querySelector('[data-tool-reset]').addEventListener('click', () => {
        tasks.forEach((t) => {
            t.checked = false;
            write('ekraf-ready-' + t.dataset.toolTask, false);
        });
        render();
    });
    render();
}
const space = document.querySelector('[data-my-space]');
if (space) {
    space.querySelectorAll('[data-my-course]').forEach((course) => {
        const total = Number(course.dataset.total);
        let count = 0;
        for (let i = 0; i < total; i++) if (read('ekraf-lesson-' + course.dataset.myCourse + '-' + i)) count++;
        course.querySelector('progress').value = count;
        course.querySelector('[data-my-course-label]').textContent = count + ' dari ' + total + ' selesai';
    });
    const cards = [...space.querySelectorAll('[data-my-listing]')];
    const update = () => {
        let count = 0;
        cards.forEach((card) => {
            card.hidden = !read('ekraf-favorite-' + card.dataset.myListing);
            if (!card.hidden) count++;
        });
        space.querySelector('[data-my-empty]').hidden = count > 0;
    };
    space.querySelectorAll('[data-my-remove]').forEach((button) =>
        button.addEventListener('click', () => {
            write('ekraf-favorite-' + button.dataset.myRemove, false);
            update();
        })
    );
    update();
    let ready = 0;
    for (let i = 0; i < 8; i++) if (read('ekraf-ready-' + i)) ready++;
    space.querySelector('[data-my-readiness]').textContent = ready + ' dari 8 langkah kesiapan usaha selesai.';
}
