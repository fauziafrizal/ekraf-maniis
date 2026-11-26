<?php

namespace App\Support;

use Illuminate\Support\Str;

final class PublicSite
{
    public static function image(?string $path, ?string $alt = null, ?string $credit = null, ?string $location = null): ?array
    {
        if (blank($path) || ! preg_match('#\A[a-zA-Z0-9_/-][a-zA-Z0-9_./ -]*\z#', $path)
            || str_starts_with($path, '/') || in_array('..', explode('/', $path), true)) {
            return null;
        }

        $file = public_path($path);
        $size = is_file($file) && is_readable($file) ? @getimagesize($file) : false;

        if (! $size || ! in_array($size['mime'], ['image/jpeg', 'image/webp', 'image/png', 'image/avif', 'image/gif'], true)) {
            return null;
        }

        return [
            'path' => $path,
            'url' => asset(implode('/', array_map('rawurlencode', explode('/', $path)))),
            'alt' => $alt,
            'credit' => $credit,
            'location' => $location,
            'width' => $size[0],
            'height' => $size[1],
        ];
    }

    /**
     * Embed the small, trusted local brand asset so its first paint does not
     * depend on a separate image request. Never embed user-uploaded content.
     */
    public static function logoSource(array $logo): string
    {
        if (($logo['path'] ?? null) !== 'images/logo.jpg') {
            return $logo['url'];
        }

        $file = public_path('images/logo-header.png');
        if (! is_file($file) || ! is_readable($file) || filesize($file) > 65536) {
            return $logo['url'];
        }

        $size = @getimagesize($file);
        if (($size['mime'] ?? null) !== 'image/png') {
            return $logo['url'];
        }

        $bytes = file_get_contents($file);

        return $bytes === false ? $logo['url'] : 'data:image/png;base64,'.base64_encode($bytes);
    }

    public static function photo(?string $path, ?string $alt, ?string $credit, ?string $location = null): ?array
    {
        return filled($alt) && filled($credit) ? self::image($path, $alt, $credit, $location) : null;
    }

    public static function contacts(array $settings): array
    {
        $contacts = [];
        $whatsapp = preg_replace('/[+\s()-]/', '', trim((string) ($settings['whatsapp'] ?? '')));

        if (preg_match('/\A[1-9][0-9]{7,14}\z/', $whatsapp)) {
            $contacts['whatsapp'] = ['label' => 'WhatsApp', 'value' => '+'.$whatsapp, 'url' => 'https://wa.me/'.$whatsapp];
        }

        $email = trim((string) ($settings['email'] ?? ''));

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mailAddress = str_replace('%40', '@', rawurlencode($email));
            $contacts['email'] = ['label' => 'Email', 'value' => $email, 'url' => 'mailto:'.$mailAddress];
        }

        foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'youtube' => 'YouTube'] as $key => $label) {
            $url = self::httpUrl($settings[$key] ?? null);

            if ($url) {
                $contacts[$key] = ['label' => $label, 'value' => Str::limit(preg_replace('#^https?://#i', '', rtrim($url, '/')), 70), 'url' => $url];
            }
        }

        return $contacts;
    }

    public static function publicBase(): ?string
    {
        $url = self::httpUrl(config('ekraf.public_url'));

        return $url && ! str_contains($url, '?') && ! str_contains($url, '#') ? rtrim($url, '/') : null;
    }

    private static function httpUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if (filter_var($url, FILTER_VALIDATE_URL)
            && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)
            && parse_url($url, PHP_URL_USER) === null) {
            return $url;
        }

        return null;
    }
}
