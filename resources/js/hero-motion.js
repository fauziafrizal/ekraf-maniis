const toggle = document.querySelector('[data-hero-motion-toggle]');
if (toggle) {
    const media = toggle.closest('.editorial-hero-media');
    const label = toggle.querySelector('[data-hero-motion-label]');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    let paused = false;
    let visible = true;
    const sync = () => {
        media.toggleAttribute('data-hero-motion-paused', paused || reduced.matches || !visible || document.hidden);
        toggle.disabled = reduced.matches;
        toggle.setAttribute('aria-pressed', String(!paused && !reduced.matches));
        label.textContent = paused ? 'Putar animasi' : 'Jeda animasi';
        toggle.querySelector('path').setAttribute('d', paused ? 'M5 3l7 5-7 5Z' : 'M6 4v8M10 4v8');
    };
    toggle.addEventListener('click', () => {
        paused = !paused;
        sync();
    });
    reduced.addEventListener('change', sync);
    document.addEventListener('visibilitychange', sync);
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(([entry]) => {
            visible = entry.isIntersecting;
            sync();
        });
        observer.observe(media);
    }
    sync();
}
