const galleryDialog = document.querySelector('[data-gallery-dialog]');

if (galleryDialog?.showModal) {
    const galleryImage = galleryDialog.querySelector('[data-gallery-image]');
    const closeGallery = galleryDialog.querySelector('[data-gallery-close]');
    let activePhoto;

    document.querySelectorAll('[data-gallery-preview]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            event.preventDefault();
            activePhoto = link;
            galleryImage.alt = link.querySelector('img').alt;
            galleryImage.src = link.href;
            galleryDialog.showModal();
        });
    });

    closeGallery.addEventListener('click', () => galleryDialog.close());
    galleryDialog.addEventListener('click', (event) => {
        if (event.target === galleryDialog) {
            galleryDialog.close();
        }
    });
    galleryDialog.addEventListener('close', () => {
        galleryImage.removeAttribute('src');
        activePhoto?.focus({ preventScroll: true });
    });
}
