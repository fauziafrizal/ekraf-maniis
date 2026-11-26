document.querySelectorAll('[data-em-filter]').forEach((root) => {
    const tools = root.querySelector('[data-em-controls]');
    const query = root.querySelector('[data-em-query]');
    const status = root.querySelector('[data-em-status]');
    const count = root.querySelector('[data-em-count]');
    const empty = root.querySelector('[data-em-empty]');
    const items = [...root.querySelectorAll('[data-em-item]')];
    if (!tools || !items.length) return;
    const normalize = (text) =>
        (text || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLocaleLowerCase('id');
    function update() {
        const words = normalize(query?.value).trim().split(/\s+/).filter(Boolean);
        let visible = 0;
        items.forEach((item) => {
            const haystack = normalize(item.textContent);
            const match =
                words.every((word) => haystack.includes(word)) &&
                (!status?.value || item.dataset.emItem === status.value);
            item.hidden = !match;
            if (match) visible++;
        });
        if (count) count.textContent = visible + ' dari ' + items.length + ' hasil';
        if (empty) empty.hidden = visible !== 0;
    }
    tools.hidden = false;
    query?.addEventListener('input', update);
    status?.addEventListener('change', update);
    update();
});

const backTop = document.querySelector('[data-em-top]');
if (backTop) {
    const update = () => {
        backTop.hidden = window.scrollY < 650;
    };
    window.addEventListener('scroll', update, { passive: true });
    backTop.addEventListener('click', () =>
        window.scrollTo({
            top: 0,
            behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
        })
    );
    update();
}

const share = document.querySelector('[data-em-share]');
if (share) {
    share.hidden = false;
    share.addEventListener('click', async () => {
        const url = document.querySelector('link[rel="canonical"]')?.href || location.href;
        const result = document.querySelector('[data-em-share-result]');
        try {
            if (navigator.share) await navigator.share({ title: document.title, url });
            else if (navigator.clipboard) {
                await navigator.clipboard.writeText(url);
                result.textContent = 'Tautan disalin.';
            } else {
                result.textContent = url;
            }
        } catch (error) {
            if (error.name !== 'AbortError') result.textContent = url;
        }
    });
}

// Also support service links when the site is published as static HTML.
const serviceCategory = document.querySelector('[data-collaboration-form] select[name="category"]');
if (serviceCategory) {
    const requested = new URLSearchParams(location.search).get('kategori');
    if ([...serviceCategory.options].some((option) => option.value === requested)) serviceCategory.value = requested;
}
