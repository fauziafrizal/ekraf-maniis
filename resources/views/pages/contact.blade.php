@extends('layouts.public')

@section('content')
@php
    $whatsappUrl = $contacts['whatsapp']['url'] ?? 'https://wa.me/628176863952';
@endphp
    <section class="contact-hero" aria-labelledby="contact-title">
        <div class="site-shell contact-hero-grid">
            <div class="contact-hero-copy">
                <p class="eyebrow">Kontak resmi Ekraf Maniis</p>
                <h1 id="contact-title">Mari Berkolaborasi Bersama Ekraf Maniis</h1>
                <p>Punya usaha kreatif, ide program, atau ingin bergabung dengan komunitas ekonomi kreatif? Mari bersama-sama mengembangkan potensi, kreativitas, dan inovasi di Kecamatan Maniis.</p>
                <div class="contact-hero-actions">
                    <a class="button-primary" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Hubungi WhatsApp</a>
                    <a class="button-secondary" href="#form-kolaborasi">Ajukan Kolaborasi</a>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-content" aria-labelledby="kanal-resmi">
        <div class="site-shell">
            <div class="contact-section-heading">
                <p class="eyebrow">Terhubung dengan pengurus</p>
                <h2 id="kanal-resmi" class="section-title">Informasi Kontak Resmi</h2>
            </div>

            <div class="contact-card-grid">
                <article class="contact-card">
                    <div class="contact-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M20.5 11.7a8.5 8.5 0 0 1-12.6 7.4L3.5 20l1-4.2A8.5 8.5 0 1 1 20.5 11.7Z" stroke="currentColor" stroke-width="1.8"/><path d="M8.4 7.8c.2-.5.5-.5.8-.5h.5c.2 0 .4.1.5.4l.7 1.7c.1.3.1.5-.1.7l-.5.6c-.2.2-.1.4.1.7.6.9 1.4 1.6 2.3 2.1.3.2.5.2.7-.1l.7-.8c.2-.2.4-.3.7-.2l1.7.8c.3.1.4.3.4.5 0 .7-.3 1.3-.8 1.7-.5.4-1.2.6-2.1.4-1.1-.2-2.4-.8-3.8-2.1-1.2-1.1-2-2.4-2.2-3.6-.2-.8.1-1.6.4-2.1Z" fill="currentColor"/></svg>
                    </div>
                    <h3>WhatsApp Admin</h3>
                    <p>0817-686-3952</p>
                    <a class="contact-card-link" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Chat WhatsApp <span aria-hidden="true">→</span></a>
                    @if (!empty($contacts['email']))
                        <a class="contact-card-link" href="{{ $contacts['email']['url'] }}">Email resmi <span aria-hidden="true">→</span></a>
                    @endif
                </article>

                <article class="contact-card">
                    <div class="contact-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.8"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                    </div>
                    <h3>Instagram Ekraf Maniis</h3>
                    <p>Ekraf Maniis</p>
                    @if (!empty($contacts['instagram']))
                        <a class="contact-card-link" href="{{ $contacts['instagram']['url'] }}" target="_blank" rel="noopener noreferrer">Kunjungi Instagram <span aria-hidden="true">→</span></a>
                    @else
                        <span class="contact-card-muted">Tautan resmi belum tersedia</span>
                    @endif
                </article>

                <article class="contact-card">
                    <div class="contact-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                    </div>
                    <h3>Lokasi Koordinasi</h3>
                    <p>Kecamatan Maniis, Kabupaten Purwakarta, Jawa Barat.</p>
                </article>

                <article class="contact-card">
                    <div class="contact-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </div>
                    <h3>Kerja Sama dan Kolaborasi</h3>
                    <p>Terbuka untuk UMKM, pelajar, sekolah, komunitas, pelaku ekonomi kreatif, dan mitra lainnya.</p>
                </article>
            </div>
        </section>
    </div>

    <section id="form-kolaborasi" class="contact-form-section" aria-labelledby="form-title">
        <div class="site-shell contact-form-grid">
            <div class="contact-form-intro">
                <p class="eyebrow">Sampaikan gagasan Anda</p>
                <h2 id="form-title" class="section-title">Ajukan Kolaborasi</h2>
                <p>Isi formulir ini dan pengajuan akan disusun otomatis sebagai pesan WhatsApp yang siap dikirim kepada admin Ekraf Maniis.</p>
            </div>

            <form class="collaboration-form" data-collaboration-form data-whatsapp-url="{{ $whatsappUrl }}" novalidate>
                <div class="form-field">
                    <label for="full-name">Nama lengkap <span aria-hidden="true">*</span></label>
                    <input id="full-name" name="name" type="text" autocomplete="name" required maxlength="100" placeholder="Masukkan nama lengkap">
                    <p class="field-error" data-error-for="name" aria-live="polite"></p>
                </div>

                <div class="form-field">
                    <label for="organization">Asal komunitas/instansi/usaha</label>
                    <input id="organization" name="organization" type="text" autocomplete="organization" maxlength="150" placeholder="Nama komunitas, instansi, atau usaha">
                </div>

                <div class="form-field">
                    <label for="whatsapp-number">Nomor WhatsApp</label>
                    <input id="whatsapp-number" name="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" placeholder="Contoh: 0812 3456 7890">
                </div>

                <div class="form-field">
                    <label for="category">Kategori keperluan</label>
                    <select id="category" name="category">
                        <option value="Bergabung dengan Ekraf">Bergabung dengan Ekraf</option>
                        <option value="Kerja sama atau kolaborasi">Kerja sama atau kolaborasi</option>
                        <option value="Pendaftaran UMKM/Pelaku Ekraf">Pendaftaran UMKM/Pelaku Ekraf</option>
                        <option value="Usulan program">Usulan program</option>
                        <option value="Permohonan pelatihan">Permohonan pelatihan</option>
                        <option value="Informasi umum">Informasi umum</option>
                    </select>
                </div>

                <div class="form-field form-field-full">
                    <label for="message">Pesan atau usulan <span aria-hidden="true">*</span></label>
                    <textarea id="message" name="message" rows="6" required maxlength="1500" placeholder="Tuliskan pesan, ide, atau usulan Anda"></textarea>
                    <p class="field-error" data-error-for="message" aria-live="polite"></p>
                </div>

                <div class="form-submit form-field-full">
                    <button class="button-primary" type="submit">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </section>

    <section class="contact-cta" aria-labelledby="join-title">
        <div class="site-shell contact-cta-grid">
            <div>
                <p class="eyebrow">Ekraf Maniis</p>
                <h2 id="join-title" class="section-title">Tumbuh dari Kolaborasi</h2>
            </div>
            <div>
                <p>Ekraf Maniis hadir sebagai wadah untuk menghubungkan ide, kreativitas, dan potensi masyarakat. Bersama kita membangun ekosistem ekonomi kreatif yang inklusif, inovatif, dan berkelanjutan.</p>
                <a class="button-primary" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Bergabung Sekarang</a>
            </div>
        </div>
    </section>
@endsection
