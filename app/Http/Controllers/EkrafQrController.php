<?php

namespace App\Http\Controllers;

use App\Models\CreativeListing;
use App\Models\EkrafCredential;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class EkrafQrController extends Controller
{
    private function qr(string $url)
    {
        $svg = (new Writer(new ImageRenderer(new RendererStyle(240), new SvgImageBackEnd)))->writeString($url);

        return response($svg, 200, ['Content-Type' => 'image/svg+xml', 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'no-store', 'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'"]);
    }

    public function listing(CreativeListing $listing)
    {
        abort_unless($listing->published, 404);

        return $this->qr(route('community.listing', $listing));
    }

    public function credential(string $token)
    {
        $item = EkrafCredential::where('token', $token)->where('active', true)->firstOrFail();

        return $this->qr(route('community.verify', $item->token));
    }
}
