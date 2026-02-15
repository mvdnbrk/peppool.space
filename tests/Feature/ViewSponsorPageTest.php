<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewSponsorPageTest extends TestCase
{
    #[Test]
    public function sponsor_page_renders_correctly(): void
    {
        $this->get(route('sponsor'))
            ->assertOk()
            ->assertSee('Support the Project')
            ->assertSee('PbvihBLgz6cFJnhYscevB4n3o85faXPG7D');
    }
}
