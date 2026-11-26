function bindStoredToggle(input, key, render) {
    let value = false;
    try {
        value = localStorage.getItem(key) === '1';
    } catch {}
    render(value);
    input.addEventListener(input.type === 'checkbox' ? 'change' : 'click', () => {
        value = input.type === 'checkbox' ? input.checked : !value;
        try {
            localStorage.setItem(key, value ? '1' : '0');
        } catch {}
        render(value);
    });
}
document.querySelectorAll('[data-em-favorite]').forEach((button) => {
    button.hidden = false;
    bindStoredToggle(button, 'ekraf-favorite-' + button.dataset.emFavorite, (value) => {
        button.setAttribute('aria-pressed', String(value));
        button.textContent = value ? 'Tersimpan di favorit' : 'Simpan favorit';
    });
});
document.querySelectorAll('[data-em-lesson]').forEach((input) => {
    input.closest('label').hidden = false;
    bindStoredToggle(input, 'ekraf-lesson-' + input.dataset.emLesson, (value) => {
        input.checked = value;
    });
});

const calculator = document.querySelector('[data-em-calculator]');
if (calculator) {
    calculator.hidden = false;
    calculator.addEventListener('submit', (event) => {
        event.preventDefault();
        const values = Object.fromEntries(new FormData(calculator));
        const cost = ['materials', 'labor', 'packaging', 'overhead'].reduce((sum, key) => sum + Number(values[key]), 0);
        const price = Math.ceil(cost * (1 + Number(values.markup) / 100));
        const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
        calculator.querySelector('[data-em-calculator-output]').textContent =
            'Total biaya: ' + rupiah.format(cost) + '. Estimasi harga jual: ' + rupiah.format(price) + '.';
    });
}
const installButton = document.querySelector('[data-em-install]');
let installPrompt;
window.addEventListener('beforeinstallprompt', (event) => {
    if (!installButton) return;
    event.preventDefault();
    installPrompt = event;
    installButton.hidden = false;
});
installButton?.addEventListener('click', async () => {
    if (!installPrompt) return;
    try {
        await installPrompt.prompt();
    } finally {
        installPrompt = null;
        installButton.hidden = true;
    }
});
const workerLink = document.querySelector('link[rel="manifest"]');
if (workerLink && 'serviceWorker' in navigator && window.isSecureContext) {
    navigator.serviceWorker.register(new URL('sw.js', workerLink.href)).catch(() => {});
}
