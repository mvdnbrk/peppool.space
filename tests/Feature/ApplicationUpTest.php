<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;

class ApplicationUpTest extends TestCase
{
    #[Test]
    public function the_application_returns_a_successful_response(): void
    {
        $this->get('/up')
            ->assertStatus(Response::HTTP_OK)
            ->assertSee('Application up');
    }
}
