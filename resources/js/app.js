import './site-motion.js';
import './site-settings.js';

const collaborationForm = document.querySelector('[data-collaboration-form]');

if (collaborationForm) {
    const adminWhatsAppUrl = collaborationForm.dataset.whatsappUrl || 'https://wa.me/628176863952';

    collaborationForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const name = collaborationForm.elements.name;
        const message = collaborationForm.elements.message;
        const nameError = collaborationForm.querySelector('[data-error-for="name"]');
        const messageError = collaborationForm.querySelector('[data-error-for="message"]');

        nameError.textContent = '';
        messageError.textContent = '';
        name.removeAttribute('aria-invalid');
        message.removeAttribute('aria-invalid');

        let valid = true;

        if (!name.value.trim()) {
            nameError.textContent = 'Nama lengkap wajib diisi.';
            name.setAttribute('aria-invalid', 'true');
            valid = false;
        }

        if (!message.value.trim()) {
            messageError.textContent = 'Pesan atau usulan wajib diisi.';
            message.setAttribute('aria-invalid', 'true');
            valid = false;
        }

        if (!valid) {
            collaborationForm.querySelector('[aria-invalid="true"]')?.focus();
            return;
        }

        const organization = collaborationForm.elements.organization.value.trim();
        const phone = collaborationForm.elements.phone.value.trim();
        const category = collaborationForm.elements.category.value;

        const lines = [
            'Halo Admin Ekraf Maniis,',
            '',
            'Saya ingin mengajukan kolaborasi:',
            `Nama: ${name.value.trim()}`,
            `Asal: ${organization || '-'}`,
            `Nomor WhatsApp: ${phone || '-'}`,
            `Kategori: ${category}`,
            '',
            'Pesan atau usulan:',
            message.value.trim(),
        ];

        window.open(`${adminWhatsAppUrl}?text=${encodeURIComponent(lines.join('\n'))}`, '_blank', 'noopener,noreferrer');
    });
}

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

document.querySelectorAll('[data-card-carousel]').forEach((carousel) => {
    const track = carousel.querySelector('[data-card-track]');
    const controls = carousel.querySelector('[data-card-controls]');
    const indicator = carousel.querySelector('[data-card-position]');
    const previous = carousel.querySelector('[data-card-prev]');
    const next = carousel.querySelector('[data-card-next]');

    if (!track || !controls || !previous || !next) {
        return;
    }

    const cards = [...track.children];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const edgeTolerance = 2;
    const dragThreshold = 7;
    let drag = null;
    let suppressClick = false;

    const maxScroll = () => Math.max(0, track.scrollWidth - track.clientWidth);

    function updateControls() {
        const viewport = track.clientWidth;
        const max = Math.max(0, track.scrollWidth - viewport);
        const position = track.scrollLeft;
        const overflows = max > edgeTolerance;

        controls.hidden = !overflows;
        track.tabIndex = overflows ? 0 : -1;
        previous.disabled = position <= edgeTolerance;
        next.disabled = position >= max - edgeTolerance;
        track.toggleAttribute('data-card-overflow', overflows);

        if (indicator && overflows) {
            const progress = Math.max(0, Math.min(1, position / max));
            const thumb = Math.max(.2, viewport / (viewport + max));

            indicator.style.setProperty('--card-position-width', `${thumb * 100}%`);
            indicator.style.setProperty('--card-position-offset', `${progress * (1 - thumb) * 100}%`);
            indicator.setAttribute('aria-valuenow', Math.round(progress * 100));
        }
    }

    function step(direction) {
        const position = track.scrollLeft;
        const max = maxScroll();
        const scrollPadding = Number.parseFloat(getComputedStyle(track).scrollPaddingLeft) || 0;
        const origin = track.getBoundingClientRect().left + track.clientLeft + scrollPadding;
        const stops = cards.map((card) => {
            const offset = card.getBoundingClientRect().left - origin + position;

            return Math.min(max, Math.max(0, offset));
        });
        const target = direction === 1
            ? (stops.find((stop) => stop > position + edgeTolerance) ?? max)
            : (stops.findLast((stop) => stop < position - edgeTolerance) ?? 0);

        track.scrollTo({
            left: target,
            behavior: reducedMotion.matches ? 'instant' : 'smooth',
        });
    }

    function endDrag() {
        drag = null;
        track.removeAttribute('data-card-dragging');
    }

    previous.addEventListener('click', () => step(-1));
    next.addEventListener('click', () => step(1));
    track.addEventListener('scroll', updateControls, { passive: true });

    track.addEventListener('keydown', (event) => {
        if (event.target !== track || maxScroll() <= edgeTolerance) {
            return;
        }

        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            event.preventDefault();
            step(event.key === 'ArrowRight' ? 1 : -1);
        }
    });

    track.addEventListener('pointerdown', (event) => {
        suppressClick = false;

        if (event.pointerType !== 'mouse' || event.button !== 0 || maxScroll() <= edgeTolerance) {
            return;
        }

        drag = {
            pointerId: event.pointerId,
            startX: event.clientX,
            startScroll: track.scrollLeft,
            moved: false,
        };
    });

    window.addEventListener('pointermove', (event) => {
        if (drag?.pointerId !== event.pointerId) {
            return;
        }

        if ((event.buttons & 1) === 0) {
            endDrag();
            return;
        }

        const distance = event.clientX - drag.startX;

        if (!drag.moved && Math.abs(distance) < dragThreshold) {
            return;
        }

        if (!drag.moved) {
            // Preserve normal link clicks until the pointer actually drags the track.
            drag.moved = true;
            suppressClick = true;
            track.setPointerCapture(event.pointerId);
            track.setAttribute('data-card-dragging', '');
        }

        event.preventDefault();
        track.scrollLeft = drag.startScroll - distance;
    });

    for (const type of ['pointerup', 'pointercancel', 'lostpointercapture']) {
        window.addEventListener(type, (event) => {
            if (drag?.pointerId === event.pointerId) {
                endDrag();
            }
        });
    }

    window.addEventListener('blur', endDrag);

    for (const type of ['dragstart', 'selectstart']) {
        track.addEventListener(type, (event) => {
            if (drag) {
                event.preventDefault();
            }
        });
    }

    track.addEventListener('click', (event) => {
        if (suppressClick && event.detail > 0) {
            event.preventDefault();
            event.stopPropagation();
            suppressClick = false;
        }
    }, true);

    const resizeObserver = new ResizeObserver(updateControls);
    resizeObserver.observe(track);
    cards.forEach((card) => resizeObserver.observe(card));

    updateControls();
});
