const progress = document.querySelector('[data-em-reading-progress]');
if (progress) {
    let pending = false;
    function update() {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        progress.style.transform = 'scaleX(' + (max > 0 ? Math.max(0, Math.min(1, window.scrollY / max)) : 0) + ')';
        pending = false;
    }
    function schedule() {
        if (!pending) {
            pending = true;
            requestAnimationFrame(update);
        }
    }
    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule, { passive: true });
    if ('ResizeObserver' in window) new ResizeObserver(schedule).observe(document.body);
    update();
}
