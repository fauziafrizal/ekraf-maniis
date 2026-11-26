<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoiceWelcomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_has_accessible_voice_welcome_and_transcript(): void
    {
        $this->withoutVite();
        $this->get('/')->assertOk()->assertSee('Dengarkan sambutan AI')->assertSee('Baca transkrip sambutan')->assertSee('Narasi disintesis, bukan rekaman manusia.')->assertSee(config('ekraf.welcome_narration'));
    }
}
