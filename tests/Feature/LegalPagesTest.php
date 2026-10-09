<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_legal_information_pages_are_available(): void
    {
        foreach (['/privacy', '/terms', '/abuse'] as $path) {
            $this->get($path)->assertOk()->assertSee('HivePaste');
        }
    }

    public function test_home_page_contains_editor_assets(): void
    {
        $this->get('/')->assertOk()->assertSee('hivepaste-editor.js');
    }
}
