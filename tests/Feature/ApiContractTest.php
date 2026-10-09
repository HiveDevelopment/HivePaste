<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\Paste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiContractTest extends TestCase
{
    use RefreshDatabase;

    private function token(string $value): ApiToken
    {
        return ApiToken::create(['name' => 'integration', 'token_hash' => hash('sha256', $value)]);
    }

    public function test_api_create_get_and_delete_contract(): void
    {
        $token = 'hp_'.str_repeat('a', 64);
        $this->token($token);
        $created = $this->withToken($token)->postJson('/api/v1/pastes', [
            'content' => 'Hello from integration', 'language' => 'log', 'expires_in' => '1d',
        ])->assertCreated()->assertJsonStructure(['id', 'url', 'raw_url', 'expires_at']);
        $uuid = $created->json('id');
        $this->assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $uuid);
        $this->getJson('/api/v1/pastes/'.$uuid)->assertOk()->assertJsonPath('content', 'Hello from integration');
        $this->withToken($token)->deleteJson('/api/v1/pastes/'.$uuid)->assertOk()->assertJsonPath('deleted', true);
        $this->getJson('/api/v1/pastes/'.$uuid)->assertNotFound();
    }

    public function test_other_api_tokens_cannot_delete_pastes(): void
    {
        $owner = 'hp_'.str_repeat('a', 64);
        $other = 'hp_'.str_repeat('b', 64);
        $this->token($owner);
        $this->token($other);
        $uuid = $this->withToken($owner)->postJson('/api/v1/pastes', ['content' => 'test'])->json('id');
        $this->withToken($other)->deleteJson('/api/v1/pastes/'.$uuid)->assertForbidden();
    }

    public function test_revoked_token_cannot_create(): void
    {
        $token = 'hp_'.str_repeat('c', 64);
        $this->token($token)->update(['revoked_at' => now()]);
        $this->withToken($token)->postJson('/api/v1/pastes', ['content' => 'test'])->assertUnauthorized();
    }

    public function test_expired_paste_is_not_accessible(): void
    {
        $paste = Paste::create([
            'slug' => Paste::newSlug(), 'content' => 'expired', 'language' => 'text',
            'visibility' => 'unlisted', 'expires_at' => now()->subMinute(),
        ]);
        $this->getJson('/api/v1/pastes/'.$paste->slug)->assertNotFound();
    }

    public function test_invalid_content_returns_validation_errors(): void
    {
        $token = 'hp_'.str_repeat('d', 64);
        $this->token($token);
        $this->withToken($token)->postJson('/api/v1/pastes', ['content' => ''])->assertUnprocessable()->assertJsonValidationErrors('content');
        config()->set('hivepaste.max_paste_bytes', 4);
        $this->withToken($token)->postJson('/api/v1/pastes', ['content' => '12345'])->assertUnprocessable()->assertJsonValidationErrors('content');
    }
}
