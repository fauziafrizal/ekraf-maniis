const mobileMenu = document.querySelector('[data-mobile-menu]');

if (mobileMenu) {
    const toggle = mobileMenu.querySelector('summary');

    mobileMenu.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileMenu.open) {
            mobileMenu.open = false;
            toggle.focus();
        }
    });

    window.matchMedia('(min-width: 64rem)').addEventListener('change', () => {
        mobileMenu.open = false;
    });
}
