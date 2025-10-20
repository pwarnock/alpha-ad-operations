<?php

namespace Tests\Unit;

use App\Models\LineItem;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LineItemTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_line_item_can_be_created()
    {
        $lineItem = LineItem::factory()->create();

        $this->assertNotNull($lineItem);
        $this->assertDatabaseHas('line_items', ['id' => $lineItem->id]);
    }

    #[Test]
    public function a_line_item_belongs_to_a_campaign()
    {
        $campaign = Campaign::factory()->create();
        $lineItem = LineItem::factory()->create(['campaign_id' => $campaign->id]);

        $this->assertInstanceOf(Campaign::class, $lineItem->campaign);
    }
}
