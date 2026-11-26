# Ekraf Maniis — deployment checklist

## Requirements
- PHP 8.4.1 or newer within the PHP 8.x range supported by composer.json. Use a currently patched release.
- Composer 2, PDO database driver, GD, curl, openssl, fileinfo, mbstring, XML and the other platform requirements.
- Node version compatible with Vite 8 to build frontend assets (Node 24 is used locally).
- A real Laravel/PHP server, not a static GitHub Pages deployment.
- Persistent database and private file storage. SQLite on an ephemeral container filesystem is not safe for production data. MySQL/MariaDB or managed PostgreSQL may be used after testing migrations and data transfer.

## Deployment
1. Back up database and private files; keep a verified restore copy outside the web root.
2. Configure the virtual host document root to the Laravel public/ directory only. Never expose the repository root. Disable directory listing and deny hidden files at the web server.
3. Install locked PHP dependencies with composer install --no-dev --prefer-dist --optimize-autoloader. Build with npm ci and npm run build in a compatible build environment; deploy public/build including manifest.json.
4. Configure .env privately on the target server. Set APP_ENV=production, APP_DEBUG=false, APP_URL and EKRAF_PUBLIC_URL to the official HTTPS address. Use SESSION_SECURE_COOKIE=true, SESSION_HTTP_ONLY=true, SESSION_SAME_SITE=lax, SESSION_ENCRYPT=true and LOG_LEVEL=warning. Enforce HTTPS on the hosting server and configure trusted reverse proxies narrowly according to the provider.
5. Generate a unique APP_KEY only for a new installation. Preserve the existing key when migrating an existing encrypted database/session environment. Never commit secrets or display them in logs.
6. Configure database credentials, persistent upload storage and permissions. Only storage and bootstrap/cache should need application write access. Do not use chmod 777.
7. Run php artisan migrate --force after backup; seed initial content only for a fresh installation. Run php artisan optimize and php artisan ekraf:hosting-check --production. Create authorized admin accounts via php artisan ekraf:admin-create; use unique long passwords.
8. Check HTTPS certificate, domain, private resource access, admin login/logout, forms, CSRF, reloads, mobile layout, error pages, backup restore and logs in the actual target environment. Recheck composer audit and npm audit regularly.

## Limitations before public launch
- Actual hosting configuration and TLS not verified by local checks.
- If behind a reverse proxy, configure only trusted proxy IPs and required forwarded headers. Do not trust arbitrary client-supplied headers.
- AI external service requires explicit configuration, usage limits and consent. Random Forest needs approved dataset/model. Verify rights to neural audio before publishing.
- Admin MFA, operational monitoring, offsite backups and an independent security review are recommended before collecting sensitive organizational records.
- BrowserSecurity middleware applies security headers to Laravel responses; configure equivalent server headers for static assets. CSP allows same-origin resources and the OpenStreetMap tile host; inline CSS remains allowed for current visual features.
- Public page HTML is not cached to avoid stale session/CSRF state. The service worker only stores the static offline fallback. Do not cache authenticated pages or credential verification at a CDN.
