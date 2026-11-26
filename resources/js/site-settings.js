const root = document.documentElement;
const preferences = document.querySelector('[data-site-preferences]');
const themeToggle = preferences?.querySelector('[data-theme-toggle]');
const musicToggle = preferences?.querySelector('[data-music-toggle]');
const musicStatus = preferences?.querySelector('[data-music-status]');
const music = document.querySelector('[data-site-music]');

function readPreference(key) {
    try {
        return localStorage.getItem(key);
    } catch {
        return null;
    }
}

function savePreference(key, value) {
    try {
        localStorage.setItem(key, value);
    } catch {
        // The controls remain usable when storage is blocked.
    }
}

if (preferences && themeToggle) {
    const preferredScheme = matchMedia('(prefers-color-scheme: dark)');
    const themeColor = document.querySelector('meta[name="theme-color"]');
    const savedTheme = readPreference('ekraf-theme');
    let selectedTheme = ['light', 'dark'].includes(savedTheme) ? savedTheme : null;

    function applyTheme() {
        const theme = selectedTheme ?? (preferredScheme.matches ? 'dark' : 'light');
        root.dataset.theme = theme;
        root.style.colorScheme = theme;
        themeToggle.setAttribute('aria-pressed', String(theme === 'dark'));

        if (themeColor) {
            themeColor.content = theme === 'dark' ? '#12291f' : '#173f35';
        }
    }

    themeToggle.addEventListener('click', () => {
        selectedTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        savePreference('ekraf-theme', selectedTheme);
        applyTheme();
    });

    preferredScheme.addEventListener('change', applyTheme);
    applyTheme();
    themeToggle.disabled = false;
    root.classList.add('has-site-preferences');
}

if (music && musicToggle && musicStatus) {
    const musicPreference = 'ekraf-music';
    musicToggle.disabled = false;
    music.volume = 0.2;

    function playMusic(autoplay = false) {
        musicStatus.textContent = '';
        music.play().catch((error) => {
            if (autoplay && error.name === 'NotAllowedError') {
                window.addEventListener('click', playAfterInteraction, { once: true });
            } else if (error.name !== 'AbortError') {
                musicStatus.textContent = 'Musik belum bisa diputar. Coba tekan tombol Musik lagi.';
            }
        });
    }

    function playAfterInteraction(event) {
        if (event?.target?.closest('[data-voice-welcome]')) return;
        playMusic();
    }

    music.addEventListener('playing', () => musicToggle.setAttribute('aria-pressed', 'true'));
    music.addEventListener('pause', () => musicToggle.setAttribute('aria-pressed', 'false'));

    musicToggle.addEventListener('click', () => {
        window.removeEventListener('click', playAfterInteraction);

        if (music.paused) {
            savePreference(musicPreference, 'on');
            playMusic();
        } else {
            savePreference(musicPreference, 'off');
            music.pause();
        }
    });

    if (readPreference(musicPreference) !== 'off') {
        playMusic(true);
    }
}
