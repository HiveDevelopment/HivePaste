<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoTest extends TestCase
{
    public function test_homepage_has_indexable_metadata(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('name="robots" content="index,follow,max-image-preview:large"', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false);
    }

    public function test_homepage_has_responsive_viewport(): void
    {
        $this->get('/')->assertOk()->assertSee('name="viewport"', false);
    }
}
