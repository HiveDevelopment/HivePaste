<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\Paste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasteTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_without_account(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_anonymous_creation_provides_a_one_time_management_key(): void
    {
        $response = $this->post('/pastes', ['content' => 'hello', 'expires_in' => '1d']);
        $paste = Paste::firstOrFail();
        $response->assertRedirect(route('pastes.show', $paste))->assertSessionHas('management_key');
        $secret = session('management_key');
        $this->assertNotSame($secret, $paste->management_token_hash);
        $this->get(route('pastes.manage', ['paste' => $paste, 'key' => $secret]))->assertOk();
        $this->get(route('pastes.edit', ['paste' => $paste, 'key' => $secret]))->assertOk();
        $this->put(route('pastes.update', $paste), ['content' => 'updated', 'management_key' => $secret, 'expires_in' => '7d'])->assertRedirect();
        $this->assertDatabaseHas('pastes', ['id' => $paste->id, 'content' => 'updated']);
        $this->delete(route('pastes.destroy', $paste), ['management_key' => $secret])->assertRedirect();
        $this->assertDatabaseMissing('pastes', ['id' => $paste->id]);
    }

    public function test_new_pastes_use_uuid_share_links_and_are_unlisted(): void
    {
        $this->post('/pastes', ['content' => 'share this'])->assertRedirect();
        $paste = Paste::firstOrFail();
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}$/', $paste->slug);
        $this->assertSame('unlisted', $paste->visibility);
        $this->get(route('pastes.show', $paste))->assertOk();
    }

    public function test_public_visibility_is_not_accepted(): void
    {
        $this->post('/pastes', ['content' => 'test', 'visibility' => 'public'])->assertSessionHasErrors('visibility');
    }

    public function test_public_link_cannot_manage_paste(): void
    {
        $paste = Paste::create(['slug' => Paste::newSlug(), 'content' => 'secret', 'language' => 'text', 'visibility' => 'unlisted']);
        $this->get(route('pastes.show', $paste))->assertOk();
        $this->get(route('pastes.manage', $paste))->assertForbidden();
        $this->get(route('pastes.edit', $paste))->assertForbidden();
        $this->put(route('pastes.update', $paste), ['content' => 'bad'])->assertForbidden();
        $this->delete(route('pastes.destroy', $paste))->assertForbidden();
    }

    public function test_anonymous_upload_can_be_disabled(): void
    {
        config()->set('hivepaste.anonymous_enabled', false);
        $this->post('/pastes', ['content' => 'hello'])->assertForbidden();
    }

    public function test_download_is_plain_text_attachment(): void
    {
        $paste = Paste::create([
            'slug' => Paste::newSlug(), 'content' => '<script>alert(1)</script>',
            'language' => 'html', 'visibility' => 'unlisted',
        ]);

        $this->get(route('pastes.download', $paste))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=utf-8')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Disposition', 'attachment; filename="'.$paste->slug.'.html"')
            ->assertSee('<script>alert(1)</script>', false);
    }

    public function test_api_requires_token(): void
    {
        $this->postJson('/api/v1/pastes', ['content' => 'hello'])->assertUnauthorized();
    }

    public function test_api_token_can_create_paste(): void
    {
        $secret = 'hp_'.str_repeat('a', 64);
        ApiToken::create(['name' => 'test', 'token_hash' => hash('sha256', $secret)]);
        $this->withToken($secret)->postJson('/api/v1/pastes', ['content' => 'hello', 'language' => 'log'])
            ->assertCreated()->assertJsonStructure(['id', 'url', 'raw_url']);
    }
}
