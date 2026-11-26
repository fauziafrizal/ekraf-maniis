const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
const cards = [...document.querySelectorAll('[data-hanging-portrait]')];
const toggle = document.querySelector('[data-hanging-motion-toggle]');
let paused = reducedMotion.matches;
const controls = [];

cards.forEach((card, index) => {
    let angle = 0;
    let animation = 0;
    let visible = false;
    let idleSince = 0;

    const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
    const draw = () => card.style.setProperty('--hang-angle', angle + 'deg');
    const stop = () => {
        cancelAnimationFrame(animation);
        animation = 0;
    };
    const reset = () => {
        stop();
        angle = 0;
        draw();
    };
    const canAnimate = () => visible && !paused && !reducedMotion.matches && !document.hidden;

    const startIdle = () => {
        if (!canAnimate() || animation) return;
        idleSince = 0;
        const period = [6.5, 7.8, 8.6][index % 3];
        const amplitude = [3.4, 3.9, 3.1][index % 3];
        const phase = index * 2.1;

        const frame = (now) => {
            if (!canAnimate()) {
                reset();
                return;
            }
            if (!idleSince) idleSince = now;
            const t = (now - idleSince) / 1000;
            const ramp = clamp((t - 0.65) / 1.5, 0, 1);
            const envelope = ramp * ramp * (3 - 2 * ramp);
            angle = Math.sin((t * 2 * Math.PI) / period + phase) * amplitude * envelope;
            draw();
            animation = requestAnimationFrame(frame);
        };

        animation = requestAnimationFrame(frame);
    };

    const sync = () => {
        if (!canAnimate()) {
            reset();
            return;
        }
        startIdle();
    };

    const observer = new IntersectionObserver(
        ([entry]) => {
            visible = entry.isIntersecting;
            sync();
        },
        { threshold: 0.15 }
    );
    observer.observe(card.closest('[data-hanging-stage]') || card);

    window.addEventListener('pagehide', reset);
    window.addEventListener('pageshow', sync);
    document.addEventListener('visibilitychange', sync);
    controls.push(sync);
});

function applyMotion() {
    if (toggle) {
        toggle.disabled = reducedMotion.matches;
        toggle.setAttribute('aria-pressed', String(!paused && !reducedMotion.matches));
        toggle.textContent = reducedMotion.matches ? 'Animasi nonaktif' : paused ? 'Putar ayunan' : 'Jeda ayunan';
    }
    controls.forEach((sync) => sync());
}

toggle?.addEventListener('click', () => {
    paused = !paused;
    applyMotion();
});
reducedMotion.addEventListener('change', () => {
    paused = reducedMotion.matches;
    applyMotion();
});
applyMotion();
