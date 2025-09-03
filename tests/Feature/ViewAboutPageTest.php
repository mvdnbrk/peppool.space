<?php

namespace Tests\Feature;

use Tests\TestCase;

class ViewAboutPageTest extends TestCase
{
    public function test_about_page_can_be_viewed(): void
    {
        $this->get('/about-pepecoin')
            ->assertStatus(200)
            ->assertSee('About Pepecoin')
            ->assertSee('About the Creator')
            ->assertSee('Connect with the Community');
    }

    public function test_about_page_contains_social_media_links(): void
    {
        $this->get('/about-pepecoin')
            ->assertStatus(200)
            ->assertSee('Follow on X')
            ->assertSee('Join Telegram')
            ->assertSee('Join Discord')
            ->assertSee('Join on Reddit')
            ->assertSee('Follow on Facebook')
            ->assertSee('Follow on TikTok')
            ->assertSee('Follow on Instagram')
            ->assertSee('Watch on YouTube')
            ->assertSee('View on GitHub');
    }
}
