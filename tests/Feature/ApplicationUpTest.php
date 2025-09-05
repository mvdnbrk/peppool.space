<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApplicationUpTest extends TestCase
{
    #[Test]
    public function the_application_returns_a_successful_response(): void
    {
        $this->get('/up')
            ->assertOk()
            ->assertSee('Application up');
    }
}
