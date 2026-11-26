const collaborationForm = document.querySelector('[data-collaboration-form]');

if (collaborationForm) {
    const submit = collaborationForm.querySelector('[data-contact-submit]');
    if (submit) submit.disabled = false;
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

        window.open(
            `${adminWhatsAppUrl}?text=${encodeURIComponent(lines.join('\n'))}`,
            '_blank',
            'noopener,noreferrer'
        );
    });
}
