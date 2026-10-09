<?php

namespace Tests\Feature;

use App\Support\IpRedactor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IpRedactionTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_redacts_ipv4_and_ipv6_and_preserves_ports(): void
    {
        $input = 'remote=203.0.113.42:25565 local=192.168.1.50 ipv6=2001:db8::1';
        $result = IpRedactor::redact($input);
        $this->assertStringNotContainsString('203.0.113.42', $result);
        $this->assertStringNotContainsString('192.168.1.50', $result);
        $this->assertStringNotContainsString('2001:db8::1', $result);
        $this->assertStringContainsString('[REDACTED IP]:25565', $result);
    }

    public function test_does_not_redact_versions_or_invalid_addresses(): void
    {
        $input = 'version 1.21.11 build 999.999.999.999';
        $this->assertSame($input, IpRedactor::redact($input));
    }

    public function test_preview_requires_valid_content_and_does_not_store_it(): void
    {
        $this->postJson(route('pastes.redaction-preview'), ['content' => 'client 203.0.113.42'])
            ->assertOk()
            ->assertJsonPath('changed', true)
            ->assertJsonPath('content', 'client [REDACTED IP]');
        $this->assertDatabaseCount('pastes', 0);
    }

    public function test_preview_rejects_missing_content(): void
    {
        $this->postJson(route('pastes.redaction-preview'), ['content' => ''])->assertUnprocessable();
    }
}
