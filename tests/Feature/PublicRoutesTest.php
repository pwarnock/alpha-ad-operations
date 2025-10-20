<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_home_page_can_be_accessed()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    #[Test]
    public function the_pricing_page_can_be_accessed()
    {
        $response = $this->get('/pricing');

        $response->assertStatus(200);
    }
}
