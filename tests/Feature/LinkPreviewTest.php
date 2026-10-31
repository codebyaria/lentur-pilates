<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_render_link_preview_tags_on_the_server_per_language()
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<meta property="og:title" content="Lentur Pilates · Gerak dulu. Sempurna belakangan.">', false)
            ->assertSee('<meta property="og:image" content="'.url('images/og/lentur-id.png').'">', false)
            ->assertSee('<meta property="og:locale" content="id_ID">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false);

        $this->get('/en/booking')
            ->assertOk()
            ->assertSee('<meta property="og:title" content="Lentur Pilates · Move first. Perfect later.">', false)
            ->assertSee('<meta property="og:image" content="'.url('images/og/lentur-en.png').'">', false)
            ->assertSee('<meta property="og:url" content="'.url('/en/booking').'">', false);
    }

    public function test_preview_images_and_icons_exist()
    {
        foreach (['images/og/lentur-id.png', 'images/og/lentur-en.png', 'favicon.svg', 'favicon.ico', 'apple-touch-icon.png'] as $file) {
            $this->assertFileExists(public_path($file));
        }

        [$width, $height] = getimagesize(public_path('images/og/lentur-id.png')) ?: [0, 0];
        $this->assertSame([1200, 630], [$width, $height]);
    }

    public function test_og_source_page_is_hidden_in_production()
    {
        $this->get('/_og')->assertOk();

        $this->app->detectEnvironment(fn () => 'production');
        $this->get('/_og')->assertNotFound();
    }
}
