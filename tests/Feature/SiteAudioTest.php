<?php

namespace Tests\Feature;

use Tests\TestCase;

class SiteAudioTest extends TestCase
{
    public function test_the_music_endpoint_supports_partial_downloads(): void
    {
        $size = filesize(public_path('audio/musik-degung-sunda.mp3'));

        $this->get('/musik-latar.mp3', ['Range' => 'bytes=0-1'])
            ->assertStatus(206)
            ->assertHeader('Content-Type', 'audio/mpeg')
            ->assertHeader('Content-Range', 'bytes 0-1/'.$size);
    }
}
