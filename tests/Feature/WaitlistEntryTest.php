<?php

namespace Tests\Feature;

use App\Models\WaitlistEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WaitlistEntryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function users_can_join_the_waitlist(): void
    {
        $response = $this->postJson(route('api.wallet.waitlist'), [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully joined the waitlist.',
            ]);

        $this->assertDatabaseHas('waitlist_entries', [
            'email' => 'test@example.com',
        ]);
    }

    #[Test]
    public function email_is_required(): void
    {
        $response = $this->postJson(route('api.wallet.waitlist'), [
            'email' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function email_must_be_valid(): void
    {
        $response = $this->postJson(route('api.wallet.waitlist'), [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function email_must_be_unique(): void
    {
        WaitlistEntry::create([
            'email' => 'duplicate@example.com',
        ]);

        $response = $this->postJson(route('api.wallet.waitlist'), [
            'email' => 'duplicate@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonFragment([
                'email' => ['You have already joined the waitlist.'],
            ]);
    }
}
