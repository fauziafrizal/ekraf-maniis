const dialog = document.querySelector('[data-visitor-dialog]');
const launcher = document.querySelector('[data-visitor-search]');
if (dialog?.showModal && launcher) {
    const input = dialog.querySelector('[data-visitor-query]');
    const status = dialog.querySelector('[data-visitor-status]');
    const items = [...dialog.querySelectorAll('[data-visitor-item]')];
    const allowed = new Set(items.map((item) => item.dataset.visitorItem));
    const tabs = [...dialog.querySelectorAll('[data-visitor-tab]')];
    const storageKey = 'ekraf-visitor-navigator-v1';
    let saved = [],
        recent = [],
        mode = 'all';
    let storageAvailable = true;
    try {
        const data = JSON.parse(localStorage.getItem(storageKey) || '{}');
        const valid = (value) =>
            Array.isArray(value) ? [...new Set(value.filter((x) => allowed.has(x)))].slice(0, 26) : [];
        saved = valid(data.saved);
        recent = valid(data.recent).slice(0, 8);
    } catch {
        storageAvailable = false;
    }
    const persist = () => {
        if (!storageAvailable) return;
        try {
            localStorage.setItem(storageKey, JSON.stringify({ saved, recent }));
        } catch {
            storageAvailable = false;
        }
    };
    const current = dialog.dataset.currentRoute;
    if (allowed.has(current)) {
        recent = [current, ...recent.filter((x) => x !== current)].slice(0, 8);
        persist();
    }
    const normalize = (value) => value.normalize('NFD').replace(/[̀-ͯ]/g, '').toLocaleLowerCase('id');
    const update = () => {
        const words = normalize(input.value).trim().split(/\s+/).filter(Boolean);
        let count = 0;
        const ordered =
            mode === 'recent'
                ? recent.map((key) => items.find((item) => item.dataset.visitorItem === key)).filter(Boolean)
                : items;
        const list = dialog.querySelector('[data-visitor-results]');
        [...ordered, ...items.filter((item) => !ordered.includes(item))].forEach((item) => list.append(item));
        items.forEach((item) => {
            const key = item.dataset.visitorItem;
            const isSaved = saved.includes(key);
            const match = words.every((word) =>
                normalize(item.textContent + ' ' + item.dataset.keywords).includes(word)
            );
            item.hidden = !match || (mode === 'saved' && !isSaved) || (mode === 'recent' && !recent.includes(key));
            if (!item.hidden) count++;
            const button = item.querySelector('[data-visitor-save]');
            button.setAttribute('aria-pressed', String(isSaved));
            button.setAttribute(
                'aria-label',
                (isSaved ? 'Hapus simpanan ' : 'Simpan ') + item.querySelector('strong').textContent
            );
        });
        tabs.forEach((tab) => tab.setAttribute('aria-pressed', String(tab.dataset.visitorTab === mode)));
        status.textContent = count ? count + ' menu ditemukan' : 'Belum ada menu yang sesuai.';
        if (!storageAvailable) status.textContent += ' Penyimpanan browser tidak tersedia.';
    };
    const open = () => {
        if (dialog.open) return;
        mode = 'all';
        input.value = '';
        update();
        dialog.showModal();
        input.focus();
    };
    launcher.addEventListener('click', (event) => {
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        open();
    });
    launcher.setAttribute('aria-haspopup', 'dialog');
    dialog.querySelector('[data-visitor-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => launcher.focus({ preventScroll: true }));
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            const r = dialog.getBoundingClientRect();
            if (event.clientX < r.left || event.clientX > r.right || event.clientY < r.top || event.clientY > r.bottom)
                dialog.close();
        }
    });
    input.addEventListener('input', update);
    tabs.forEach((tab) =>
        tab.addEventListener('click', () => {
            mode = tab.dataset.visitorTab;
            update();
        })
    );
    items.forEach((item) =>
        item.querySelector('[data-visitor-save]').addEventListener('click', () => {
            const key = item.dataset.visitorItem;
            saved = saved.includes(key) ? saved.filter((x) => x !== key) : [...saved, key];
            persist();
            update();
        })
    );
    dialog.querySelector('[data-visitor-clear]').addEventListener('click', () => {
        recent = [];
        persist();
        update();
    });
    document.addEventListener('keydown', (event) => {
        if (
            (event.metaKey || event.ctrlKey) &&
            event.key.toLowerCase() === 'k' &&
            !document.querySelector('dialog[open]:not([data-visitor-dialog])')
        ) {
            event.preventDefault();
            if (dialog.open) dialog.close();
            else open();
        }
        if (!dialog.open || !['ArrowDown', 'ArrowUp'].includes(event.key)) return;
        const links = items
            .filter((item) => !item.hidden)
            .sort((a, b) => (a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1))
            .map((item) => item.querySelector('a'));
        const index = links.indexOf(document.activeElement);
        if (document.activeElement !== input && index < 0) return;
        if (!links.length) return;
        event.preventDefault();
        links[
            index < 0
                ? event.key === 'ArrowDown'
                    ? 0
                    : links.length - 1
                : (index + (event.key === 'ArrowDown' ? 1 : -1) + links.length) % links.length
        ].focus();
    });
    update();
}
