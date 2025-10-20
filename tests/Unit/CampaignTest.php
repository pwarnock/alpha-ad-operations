<?php

namespace Tests\Unit;

use App\Models\Campaign;
use App\Models\Advertiser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_campaign_can_be_created()
    {
        $campaign = Campaign::factory()->create();

        $this->assertNotNull($campaign);
        $this->assertDatabaseHas('campaigns', ['id' => $campaign->id]);
    }

    #[Test]
    public function a_campaign_belongs_to_an_advertiser()
    {
        $advertiser = Advertiser::factory()->create();
        $campaign = Campaign::factory()->create(['advertiser_id' => $advertiser->id]);

        $this->assertInstanceOf(Advertiser::class, $campaign->advertiser);
    }

    #[Test]
    public function a_campaign_has_many_line_items()
    {
        $campaign = Campaign::factory()->create();
        \App\Models\LineItem::factory()->count(2)->create(['campaign_id' => $campaign->id]);

        $this->assertCount(2, $campaign->lineItems);
        $this->assertInstanceOf(\App\Models\LineItem::class, $campaign->lineItems->first());
    }
}
