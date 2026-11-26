const motion = window.matchMedia('(prefers-reduced-motion: reduce)');

if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (!entry.isIntersecting) {
                    continue;
                }

                observer.unobserve(entry.target);

                if (!motion.matches && !entry.target.matches(':focus-within')) {
                    entry.target.setAttribute('data-revealed', '');
                }
            }
        },
        { rootMargin: '0px 0px -5% 0px' }
    );

    document
        .querySelectorAll(
            [
                '[data-reveal]',
                '.editorial-page-heading',
                '.profile-intro-grid > *',
                '.leadership-card',
                '.leadership-member',
                '[data-page="contact"] > :is(section, header, div)',
            ].join(',')
        )
        .forEach((element) => observer.observe(element));
}
