<?php

namespace Tests\Unit;

use App\Models\Advertiser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AdvertiserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_advertiser_can_be_created()
    {
        $advertiser = Advertiser::factory()->create();

        $this->assertNotNull($advertiser);
        $this->assertDatabaseHas('advertisers', ['id' => $advertiser->id]);
    }

    #[Test]
    public function an_advertiser_has_many_campaigns()
    {
        $advertiser = Advertiser::factory()->create();
        \App\Models\Campaign::factory()->count(3)->create(['advertiser_id' => $advertiser->id]);

        $this->assertCount(3, $advertiser->campaigns);
        $this->assertInstanceOf(\App\Models\Campaign::class, $advertiser->campaigns->first());
    }
}
