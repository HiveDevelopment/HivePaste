<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HivePanelPublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_integration_is_disabled_by_default(): void
    {
        config()->set('hivepaste.hivepanel_public_enabled', false);
        $this->postJson('/api/v1/integrations/hivepanel/pastes', ['content' => 'test'])->assertStatus(503);
    }

    public function test_anonymous_share_is_unlisted_and_expires_in_seven_days(): void
    {
        config()->set('hivepaste.hivepanel_public_enabled', true);
        $result = $this->postJson('/api/v1/integrations/hivepanel/pastes', [
            'title' => 'server.log', 'content' => 'Server started', 'language' => 'log',
            'expires_in' => '30d',
        ]);
        $result->assertCreated()->assertJsonPath('visibility', 'unlisted');
        $this->assertEqualsWithDelta(now()->addDays(7)->timestamp, strtotime($result->json('expires_at')), 5);
    }

    public function test_binary_data_is_rejected(): void
    {
        config()->set('hivepaste.hivepanel_public_enabled', true);
        $this->postJson('/api/v1/integrations/hivepanel/pastes', ['content' => "abc\0def"])->assertUnprocessable();
    }
}
