const get = (k) => {
    try {
        return localStorage.getItem(k);
    } catch {
        return null;
    }
};
const set = (k, v) => {
    try {
        localStorage.setItem(k, String(v));
    } catch {}
};
document.querySelectorAll('[data-print]').forEach((b) => {
    b.hidden = false;
    b.addEventListener('click', () => window.print());
});
const tips = [
    'Catat biaya bahan dan kemasan sebelum menentukan harga jual.',
    'Gunakan cahaya alami untuk foto produk.',
    'Tanyakan kebutuhan pelanggan sebelum membuat produk baru.',
    'Buat nama dan identitas merek yang mudah dikenali.',
    'Pisahkan pencatatan keuangan usaha dan pribadi.',
    'Pastikan klaim dan foto promosi sesuai keadaan produk.',
    'Minta izin sebelum mempublikasikan karya orang lain.',
];
const tip = document.querySelector('[data-daily-tip]');
if (tip) {
    let i = Math.floor(Date.now() / 86400000) % tips.length;
    const b = document.querySelector('[data-next-tip]');
    const render = () => (tip.textContent = tips[i]);
    render();
    b.hidden = false;
    b.addEventListener('click', () => {
        i = (i + 1) % tips.length;
        render();
    });
}
const quiz = document.querySelector('[data-quiz-form]');
if (quiz) {
    quiz.hidden = false;
    const result = quiz.querySelector('[data-quiz-result]');
    const render = () => {
        const best = Math.max(0, Math.min(5, Number(get('ekraf-quiz-best')) || 0));
        let lessons = 0;
        for (const [kind, total] of [
            ['academy', 6],
            ['student', 4],
        ])
            for (let i = 0; i < total; i++) if (get('ekraf-lesson-' + kind + '-' + i) === '1') lessons++;
        const points = best * 20 + lessons * 10;
        document.querySelector('[data-quiz-points]').textContent = points + ' poin';
        document.querySelector('[data-quiz-badge]').textContent =
            points >= 150
                ? 'Lencana Penjelajah Kreatif'
                : points >= 100
                  ? 'Lencana Pembelajar Kreatif'
                  : points >= 40
                    ? 'Lencana Langkah Awal'
                    : 'Belum memperoleh lencana.';
    };
    quiz.addEventListener('submit', (e) => {
        e.preventDefault();
        const v = new FormData(quiz);
        const correct = ['0', '1', '2', '0', '1'];
        let score = correct.filter((a, i) => v.get('q' + i) === a).length;
        set('ekraf-quiz-best', Math.max(score, Number(get('ekraf-quiz-best')) || 0));
        result.textContent = 'Skor: ' + score + '/5. Jawaban benar: 1 A, 2 B, 3 C, 4 A, 5 B.';
        render();
    });
    render();
}
const notify = document.querySelector('[data-alert-permission]');
if (notify && 'Notification' in window && window.isSecureContext) {
    notify.hidden = false;
    notify.addEventListener('click', async () => {
        const status = document.querySelector('[data-alert-status]');
        try {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                status.textContent = 'Izin notifikasi tidak diberikan. Gunakan kalender perangkat.';
                return;
            }
            status.textContent = 'Notifikasi aktif selama halaman ini terbuka.';
            const check = () => {
                document.querySelectorAll('[data-event-time]').forEach((el) => {
                    const t = Date.parse(el.dataset.eventTime),
                        delta = t - Date.now(),
                        key = 'ekraf-event-notified-' + el.dataset.eventTime + '-' + el.dataset.eventTitle;
                    if (delta >= 0 && delta <= 3600000 && !get(key)) {
                        try {
                            new Notification('Agenda Ekraf Maniis', {
                                body: el.dataset.eventTitle + ' dimulai dalam satu jam.',
                            });
                            set(key, 1);
                        } catch {
                            status.textContent = 'Notifikasi tidak didukung pada perangkat ini. Gunakan kalender.';
                        }
                    }
                });
            };
            check();
            if (!notify.dataset.running) {
                notify.dataset.running = '1';
                setInterval(check, 60000);
            }
        } catch {
            status.textContent = 'Notifikasi tidak tersedia. Gunakan kalender perangkat.';
        }
    });
}
const mapButton = document.querySelector('[data-map-load]');
if (mapButton) {
    mapButton.hidden = false;
    mapButton.addEventListener('click', async () => {
        mapButton.disabled = true;
        try {
            const L = await import('leaflet');
            await import('leaflet/dist/leaflet.css');
            const el = document.querySelector('[data-ekraf-map]');
            el.hidden = false;
            const map = L.map(el);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);
            const points = [];
            document.querySelectorAll('[data-map-point]').forEach((node) => {
                const point = [Number(node.dataset.lat), Number(node.dataset.lng)];
                points.push(point);
                const title = document.createElement('a');
                title.textContent = node.querySelector('h3').textContent;
                title.href = node.querySelector('h3 a').href;
                L.circleMarker(point, { radius: 8, color: '#173f35', fillColor: '#e3b86a', fillOpacity: 0.9 })
                    .addTo(map)
                    .bindPopup(title);
            });
            map.fitBounds(points, { maxZoom: 15, padding: [25, 25] });
        } catch {
            mapButton.disabled = false;
            mapButton.textContent = 'Peta gagal dimuat, coba lagi';
        }
    });
}
const panoramaButton = document.querySelector('[data-panorama-load]');
if (panoramaButton) {
    panoramaButton.hidden = false;
    panoramaButton.addEventListener('click', async () => {
        panoramaButton.disabled = true;
        try {
            await import('pannellum');
            await import('pannellum/build/pannellum.css');
            const el = document.querySelector('[data-panorama]');
            el.hidden = false;
            window.pannellum.viewer(el, {
                type: 'equirectangular',
                panorama: el.dataset.panorama,
                autoLoad: true,
                showFullscreenCtrl: true,
                escapeHTML: true,
            });
        } catch {
            panoramaButton.disabled = false;
            panoramaButton.textContent = 'Tur gagal dimuat, coba lagi';
        }
    });
}
