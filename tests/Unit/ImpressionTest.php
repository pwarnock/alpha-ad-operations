<?php

namespace Tests\Unit;

use App\Models\Impression;
use App\Models\LineItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ImpressionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_impression_can_be_created()
    {
        $impression = Impression::factory()->create();

        $this->assertNotNull($impression);
        $this->assertDatabaseHas('impressions', ['id' => $impression->id]);
    }

    #[Test]
    public function an_impression_belongs_to_a_line_item()
    {
        $lineItem = LineItem::factory()->create();
        $impression = Impression::factory()->create(['line_item_id' => $lineItem->id]);

        $this->assertInstanceOf(LineItem::class, $impression->lineItem);
    }
}
