<?php

namespace Tests\Feature\Api;

use App\Models\Block;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlocksListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function blocks_list_returns_collection_when_no_start_height(): void
    {
        Block::factory()->count(5)->create();

        $response = $this->get(route('api.blocks.list'));

        $response->assertOk();

        // Debug: dump the actual response to see structure
        $responseData = $response->json();
        if (! array_key_exists('data', $responseData)) {
            // If no 'data' key, it might be a direct array
            $response->assertJsonStructure([
                '*' => [
                    'id', 'height', 'version', 'timestamp', 'tx_count', 'size', 'difficulty', 'nonce', 'merkle_root',
                ],
            ]);
        } else {
            $response->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'height', 'version', 'timestamp', 'tx_count', 'size', 'difficulty', 'nonce', 'merkle_root',
                    ],
                ],
            ]);
        }
    }

    #[Test]
    public function blocks_list_with_non_numeric_height_returns_400(): void
    {
        $this->get(route('api.blocks.list', ['startHeight' => 'abc']))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'error' => 'Bad request',
                'code' => 400,
            ]);
    }

    #[Test]
    public function blocks_list_with_unknown_height_returns_404(): void
    {
        Block::factory()->create(['height' => 100]);

        $this->get(route('api.blocks.list', ['startHeight' => '999999999']))
            ->assertNotFound()
            ->assertJson([
                'error' => 'Block not found',
                'code' => 404,
            ]);
    }
}
