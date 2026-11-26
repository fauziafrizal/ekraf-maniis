<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EkrafMedia
{
    public static function saveImage(UploadedFile $file): string
    {
        $size = @getimagesize($file->getPathname());
        if (! $size || $size[0] * $size[1] > 16000000 || ! in_array($size['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw ValidationException::withMessages(['photo' => 'Foto harus JPG, PNG atau WebP dengan maksimal 16 juta piksel.']);
        }
        $img = @imagecreatefromstring(file_get_contents($file->getPathname()));
        if (! $img) {
            throw ValidationException::withMessages(['photo' => 'Foto tidak dapat dibaca.']);
        }
        $width = imagesx($img);
        $height = imagesy($img);
        if (max($width, $height) > 1600) {
            $scale = 1600 / max($width, $height);
            $scaled = imagescale($img, max(1, (int) ($width * $scale)), max(1, (int) ($height * $scale)));
            if (! $scaled) {
                throw ValidationException::withMessages(['photo' => 'Foto tidak dapat diproses.']);
            }
            $img = $scaled;
        }
        ob_start();
        imagejpeg($img, null, 86);
        $bytes = ob_get_clean();
        $path = 'ekraf-media/'.Str::uuid().'.jpg';
        abort_unless(Storage::disk('local')->put($path, $bytes), 500);

        return $path;
    }
}
