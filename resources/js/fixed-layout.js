const header = document.querySelector('[data-site-header]');
const footer = document.querySelector('[data-site-footer]');
const footerDetails = footer?.querySelector('details');
const mobileMenu = document.querySelector('[data-mobile-menu]');
if (header && footer) {
    const root = document.documentElement;
    const measure = () => {
        root.style.setProperty('--em-header-height', Math.ceil(header.getBoundingClientRect().height) + 'px');
        root.style.setProperty('--em-footer-height', Math.ceil(footer.getBoundingClientRect().height) + 'px');
    };
    measure();
    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(measure);
        observer.observe(header);
        observer.observe(footer);
    }
    window.addEventListener('resize', measure, { passive: true });
    document.fonts?.ready.then(measure);
    footerDetails?.addEventListener('toggle', () => {
        if (footerDetails.open && mobileMenu) mobileMenu.open = false;
        measure();
    });
    mobileMenu?.addEventListener('toggle', () => {
        if (mobileMenu.open && footerDetails) footerDetails.open = false;
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && footerDetails?.open) {
            footerDetails.open = false;
            footerDetails.querySelector('summary')?.focus();
        }
    });
    document.addEventListener('click', (event) => {
        if (footerDetails?.open && !footer.contains(event.target)) footerDetails.open = false;
    });
}
