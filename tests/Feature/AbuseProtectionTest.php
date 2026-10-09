<?php

namespace Tests\Feature;

use App\Models\Paste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AbuseProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonymous_paste_with_private_key_is_rejected(): void
    {
        $this->post('/pastes', ['content' => '-----BEGIN PRIVATE KEY-----\nabc'])
            ->assertSessionHasErrors('content');
        $this->assertDatabaseCount('pastes', 0);
    }

    public function test_secret_detection_can_be_disabled_by_host(): void
    {
        config()->set('hivepaste.secret_detection', false);
        $this->post('/pastes', ['content' => '-----BEGIN PRIVATE KEY-----'])->assertRedirect();
        $this->assertDatabaseCount('pastes', 1);
    }

    public function test_turnstile_requires_response_when_enabled(): void
    {
        config()->set('hivepaste.turnstile_site_key', 'site');
        config()->set('hivepaste.turnstile_secret_key', 'secret');
        $this->post('/pastes', ['content' => 'hello'])->assertSessionHasErrors('captcha');
    }

    public function test_turnstile_verification_succeeds_with_provider_response(): void
    {
        config()->set('hivepaste.turnstile_site_key', 'site');
        config()->set('hivepaste.turnstile_secret_key', 'secret');
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);
        $this->post('/pastes', ['content' => 'hello', 'cf-turnstile-response' => 'valid'])->assertRedirect();
        $this->assertDatabaseCount('pastes', 1);
    }

    public function test_anonymous_report_is_stored(): void
    {
        $paste = Paste::create(['slug' => Paste::newSlug(), 'content' => 'hello', 'language' => 'text', 'visibility' => 'unlisted']);
        $this->post(route('pastes.report', $paste), ['reason' => 'spam', 'details' => 'test'])->assertRedirect();
        $this->assertDatabaseHas('paste_reports', ['paste_id' => $paste->id, 'reason' => 'spam']);
    }

    public function test_honeypot_rejects_automated_report(): void
    {
        $paste = Paste::create(['slug' => Paste::newSlug(), 'content' => 'hello', 'language' => 'text', 'visibility' => 'unlisted']);
        $this->post(route('pastes.report', $paste), ['reason' => 'spam', 'website' => 'bot'])->assertSessionHasErrors('website');
        $this->assertDatabaseCount('paste_reports', 0);
    }
}
