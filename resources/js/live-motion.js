const motion = matchMedia('(prefers-reduced-motion: reduce)');
const toggle = document.querySelector('[data-live-toggle]');
if (toggle) {
    toggle.disabled = false;
    let playing = false;
    function apply() {
        document.documentElement.toggleAttribute('data-em-play-ticker', playing && !motion.matches);
        toggle.hidden = motion.matches;
        toggle.setAttribute('aria-pressed', String(playing && !motion.matches));
        toggle.textContent = playing && !motion.matches ? 'Jeda' : 'Putar';
        toggle.setAttribute(
            'aria-label',
            playing && !motion.matches ? 'Jeda animasi informasi' : 'Putar animasi informasi'
        );
    }
    toggle.addEventListener('click', () => {
        playing = !playing;
        apply();
    });
    motion.addEventListener('change', apply);
    apply();
}
