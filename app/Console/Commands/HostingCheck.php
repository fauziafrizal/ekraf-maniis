<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HostingCheck extends Command
{
    protected $signature = 'ekraf:hosting-check {--production : Include deployment configuration checks}';

    protected $description = 'Verify hosting requirements without displaying configuration secrets';

    public function handle(): int
    {
        $checks = [
            'PHP >= 8.4.1' => version_compare(PHP_VERSION, '8.4.1', '>='),
            'Required PHP extensions' => collect(['ctype', 'curl', 'dom', 'fileinfo', 'gd', 'mbstring', 'openssl', 'pdo', 'session', 'tokenizer', 'xml'])->every(fn ($ext) => extension_loaded($ext)),
            'Application key configured' => filled(config('app.key')),
            'Debug disabled' => ! config('app.debug'),
            'Writable storage' => is_writable(storage_path('app/private')) && is_writable(storage_path('framework')) && is_writable(storage_path('logs')),
            'Writable bootstrap cache' => is_writable(base_path('bootstrap/cache')),
            'Vite production manifest' => is_file(public_path('build/manifest.json')) && ! is_file(public_path('hot')),
            'Public directory separated' => ! is_file(public_path('.env')) && ! is_file(public_path('database.sqlite')) && ! is_dir(public_path('vendor')),
        ];
        try {
            DB::connection()->getPdo();
            $checks['Database and core tables'] = collect(['users', 'sessions', 'cache', 'creative_listings', 'ekraf_documents', 'ekraf_votes', 'ekraf_posts', 'ekraf_credentials'])->every(fn ($table) => Schema::hasTable($table));
        } catch (\Throwable) {
            $checks['Database and core tables'] = false;
        }
        if ($this->option('production')) {
            $checks['Production environment'] = app()->isProduction();
            $checks['HTTPS application URL'] = str_starts_with((string) config('app.url'), 'https://');
            $checks['Secure session cookie'] = config('session.secure') === true;
            $checks['Encrypted session storage'] = (bool) config('session.encrypt');
            $checks['HttpOnly session cookie'] = (bool) config('session.http_only');
            $checks['Warning or higher log level'] = in_array(config('logging.channels.single.level'), ['warning', 'error', 'critical', 'alert', 'emergency'], true);
        }
        $this->table(['Check', 'Status'], collect($checks)->map(fn ($ok, $name) => [$name, $ok ? 'PASS' : 'FAIL'])->values()->all());
        $this->warn('Verify actual web document root, HTTPS, proxy settings, persistent storage, backup/restore and administrator access on the hosting server.');

        return in_array(false, $checks, true) ? self::FAILURE : self::SUCCESS;
    }
}
