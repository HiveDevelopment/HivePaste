<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreviewValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_enforces_utf8_byte_limit_and_does_not_store_content(): void
    {
        config()->set('hivepaste.max_paste_bytes', 4);
        $this->postJson(route('pastes.redaction-preview'), ['content' => 'ééé'])
            ->assertUnprocessable()->assertJsonValidationErrors('content');
        $this->assertDatabaseCount('pastes', 0);
    }
}
