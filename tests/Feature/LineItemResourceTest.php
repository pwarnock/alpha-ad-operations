<?php

namespace Tests\Feature;

use App\Models\Advertiser;
use App\Models\Campaign;
use App\Models\LineItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LineItemResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Advertiser $advertiser;
    protected Campaign $campaign;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->advertiser = Advertiser::factory()->create();
        $this->campaign = Campaign::factory()->create(['advertiser_id' => $this->advertiser->id]);
    }

    #[Test]
    public function list_page_can_be_rendered()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->assertSuccessful();
    }

    #[Test]
    public function create_page_can_be_rendered()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\CreateLineItem::class)
            ->assertSuccessful();
    }

    #[Test]
    public function edit_page_can_be_rendered()
    {
        $lineItem = LineItem::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\EditLineItem::class, [
                'record' => $lineItem->getKey(),
            ])
            ->assertSuccessful();
    }

    #[Test]
    public function view_page_can_be_rendered()
    {
        $lineItem = LineItem::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ViewLineItem::class, [
                'record' => $lineItem->getKey(),
            ])
            ->assertSuccessful();
    }

    #[Test]
    public function can_create_line_item()
    {
        $newData = [
            'campaign_id' => $this->campaign->id,
            'name' => 'Test Line Item',
            'status' => 'draft',
            'impressions_goal' => 100000,
            'clicks_goal' => 1000,
            'budget' => 5000.00,
            'ad_size' => '300x250',
            'ad_zone' => 'homepage_above_fold',
            'pricing_model' => 'CPM',
            'rate' => 2.50,
            'start_date' => '2023-01-01',
            'end_date' => '2023-12-31',
            'targeting' => [
                'geo' => 'US',
                'device' => 'desktop',
            ],
            'notes' => 'Line item notes',
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\CreateLineItem::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('line_items', [
            'name' => 'Test Line Item',
            'campaign_id' => $this->campaign->id,
            'status' => 'draft',
            'impressions_goal' => 100000,
            'clicks_goal' => 1000,
            'budget' => 5000.00,
            'ad_size' => '300x250',
            'ad_zone' => 'homepage_above_fold',
            'pricing_model' => 'CPM',
            'rate' => 2.50,
        ]);
    }

    #[Test]
    public function can_validate_required_fields_when_creating_line_item()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\CreateLineItem::class)
            ->fillForm([
                'campaign_id' => '',
                'name' => '',
                'status' => '',
                'budget' => '',
                'ad_size' => '',
                'ad_zone' => '',
                'pricing_model' => '',
                'rate' => '',
                'start_date' => '',
                'end_date' => '',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'campaign_id',
                'name',
                'status',
                'budget',
                'ad_size',
                'ad_zone',
                'pricing_model',
                'rate',
                'start_date',
                'end_date',
            ]);
    }

    #[Test]
    public function can_validate_end_date_after_start_date()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\CreateLineItem::class)
            ->fillForm([
                'campaign_id' => $this->campaign->id,
                'name' => 'Test Line Item',
                'status' => 'draft',
                'budget' => 1000,
                'ad_size' => '300x250',
                'ad_zone' => 'test_zone',
                'pricing_model' => 'CPM',
                'rate' => 5.00,
                'start_date' => '2023-12-31',
                'end_date' => '2023-01-01', // Before start date
            ])
            ->call('create')
            ->assertHasFormErrors(['end_date']);
    }

    #[Test]
    public function can_update_line_item()
    {
        $lineItem = LineItem::factory()->create([
            'name' => 'Original Line Item',
            'status' => 'draft',
            'budget' => 3000.00,
        ]);

        $updatedData = [
            'campaign_id' => $this->campaign->id,
            'name' => 'Updated Line Item',
            'status' => 'active',
            'impressions_goal' => 200000,
            'clicks_goal' => 2000,
            'budget' => 8000.00,
            'ad_size' => '728x90',
            'ad_zone' => 'sidebar_top',
            'pricing_model' => 'CPC',
            'rate' => 1.50,
            'start_date' => '2023-02-01',
            'end_date' => '2023-11-30',
            'notes' => 'Updated notes',
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\EditLineItem::class, [
                'record' => $lineItem->getKey(),
            ])
            ->fillForm($updatedData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('line_items', [
            'id' => $lineItem->id,
            'name' => 'Updated Line Item',
            'status' => 'active',
            'budget' => 8000.00,
            'ad_size' => '728x90',
            'ad_zone' => 'sidebar_top',
            'pricing_model' => 'CPC',
            'rate' => 1.50,
        ]);
    }

    #[Test]
    public function can_delete_line_item()
    {
        $lineItem = LineItem::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\EditLineItem::class, [
                'record' => $lineItem->getKey(),
            ])
            ->call('delete')
            ->assertNotified();

        $this->assertModelMissing($lineItem);
    }

    #[Test]
    public function can_delete_line_item_from_list()
    {
        $lineItem = LineItem::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->callTableAction('delete', $lineItem)
            ->assertNotified();

        $this->assertModelMissing($lineItem);
    }

    #[Test]
    public function can_view_line_item_details()
    {
        $lineItem = LineItem::factory()->create([
            'name' => 'Test Line Item',
            'campaign_id' => $this->campaign->id,
            'status' => 'active',
            'budget' => 5000.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ViewLineItem::class, [
                'record' => $lineItem->getKey(),
            ])
            ->assertFormSet([
                'name' => 'Test Line Item',
                'campaign_id' => $this->campaign->id,
                'status' => 'active',
                'budget' => 5000.00,
            ]);
    }

    #[Test]
    public function can_search_line_items()
    {
        $lineItem1 = LineItem::factory()->create(['name' => 'Alpha Line Item']);
        $lineItem2 = LineItem::factory()->create(['name' => 'Beta Line Item']);
        $lineItem3 = LineItem::factory()->create(['name' => 'Gamma Line Item']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->searchTable('Alpha')
            ->assertCanSeeTableRecords([$lineItem1])
            ->assertCanNotSeeTableRecords([$lineItem2, $lineItem3]);
    }

    #[Test]
    public function can_filter_line_items_by_status()
    {
        $draftLineItem = LineItem::factory()->create(['status' => 'draft']);
        $activeLineItem = LineItem::factory()->create(['status' => 'active']);
        $pausedLineItem = LineItem::factory()->create(['status' => 'paused']);
        $completedLineItem = LineItem::factory()->create(['status' => 'completed']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->filterTable('status', 'active')
            ->assertCanSeeTableRecords([$activeLineItem])
            ->assertCanNotSeeTableRecords([$draftLineItem, $pausedLineItem, $completedLineItem]);
    }

    #[Test]
    public function can_filter_line_items_by_campaign()
    {
        $campaign1 = Campaign::factory()->create();
        $campaign2 = Campaign::factory()->create();
        
        $lineItem1 = LineItem::factory()->create(['campaign_id' => $campaign1->id]);
        $lineItem2 = LineItem::factory()->create(['campaign_id' => $campaign1->id]);
        $lineItem3 = LineItem::factory()->create(['campaign_id' => $campaign2->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->filterTable('campaign', $campaign1->id)
            ->assertCanSeeTableRecords([$lineItem1, $lineItem2])
            ->assertCanNotSeeTableRecords([$lineItem3]);
    }

    #[Test]
    public function can_filter_line_items_by_ad_size()
    {
        $leaderboardLineItem = LineItem::factory()->create(['ad_size' => '728x90']);
        $mediumRectLineItem = LineItem::factory()->create(['ad_size' => '300x250']);
        $skyscraperLineItem = LineItem::factory()->create(['ad_size' => '160x600']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->filterTable('ad_size', '300x250')
            ->assertCanSeeTableRecords([$mediumRectLineItem])
            ->assertCanNotSeeTableRecords([$leaderboardLineItem, $skyscraperLineItem]);
    }

    #[Test]
    public function can_sort_line_items()
    {
        $lineItem1 = LineItem::factory()->create(['name' => 'A Line Item', 'budget' => 1000]);
        $lineItem2 = LineItem::factory()->create(['name' => 'B Line Item', 'budget' => 5000]);
        $lineItem3 = LineItem::factory()->create(['name' => 'C Line Item', 'budget' => 3000]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->sortTable('budget', 'desc')
            ->assertCanSeeTableRecordsInOrder([$lineItem2, $lineItem3, $lineItem1]);
    }

    #[Test]
    public function can_bulk_delete_line_items()
    {
        $lineItem1 = LineItem::factory()->create();
        $lineItem2 = LineItem::factory()->create();
        $lineItem3 = LineItem::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->callTableBulkAction('delete', [$lineItem1, $lineItem2])
            ->assertNotified();

        $this->assertModelMissing($lineItem1);
        $this->assertModelMissing($lineItem2);
        $this->assertModelExists($lineItem3);
    }

    #[Test]
    public function displays_correct_badge_colors_for_status()
    {
        $draftLineItem = LineItem::factory()->create(['status' => 'draft']);
        $activeLineItem = LineItem::factory()->create(['status' => 'active']);
        $pausedLineItem = LineItem::factory()->create(['status' => 'paused']);
        $completedLineItem = LineItem::factory()->create(['status' => 'completed']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->assertTableColumnStateSet('status', 'gray', $draftLineItem)
            ->assertTableColumnStateSet('status', 'success', $activeLineItem)
            ->assertTableColumnStateSet('status', 'warning', $pausedLineItem)
            ->assertTableColumnStateSet('status', 'info', $completedLineItem);
    }

    #[Test]
    public function displays_correct_pacing_colors()
    {
        $underDeliveringLineItem = LineItem::factory()->create([
            'impressions_goal' => 100000,
            'impressions_delivered' => 10000, // 10%
        ]);
        
        $onTrackLineItem = LineItem::factory()->create([
            'impressions_goal' => 100000,
            'impressions_delivered' => 50000, // 50%
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->assertTableColumnStateSet('impressions_pacing', 'danger', $underDeliveringLineItem)
            ->assertTableColumnStateSet('impressions_pacing', 'success', $onTrackLineItem);
    }

    #[Test]
    public function can_select_campaign_from_relationship()
    {
        $campaigns = Campaign::factory()->count(3)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\CreateLineItem::class)
            ->assertFormFieldExists('campaign_id')
            ->assertFormFieldIsSelectable('campaign_id');
    }

    #[Test]
    public function spent_field_is_disabled_in_form()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\CreateLineItem::class)
            ->assertFormFieldDisabled('spent');
    }

    #[Test]
    public function can_select_ad_size_options()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\CreateLineItem::class)
            ->assertFormFieldExists('ad_size')
            ->assertFormFieldIsSelectable('ad_size');
    }

    #[Test]
    public function can_select_pricing_model_options()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\CreateLineItem::class)
            ->assertFormFieldExists('pricing_model')
            ->assertFormFieldIsSelectable('pricing_model');
    }

    #[Test]
    public function can_search_by_ad_zone()
    {
        $lineItem1 = LineItem::factory()->create(['ad_zone' => 'homepage_above_fold']);
        $lineItem2 = LineItem::factory()->create(['ad_zone' => 'sidebar_bottom']);
        $lineItem3 = LineItem::factory()->create(['ad_zone' => 'footer']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->searchTable('homepage')
            ->assertCanSeeTableRecords([$lineItem1])
            ->assertCanNotSeeTableRecords([$lineItem2, $lineItem3]);
    }

    #[Test]
    public function can_search_by_ad_size()
    {
        $lineItem1 = LineItem::factory()->create(['ad_size' => '728x90']);
        $lineItem2 = LineItem::factory()->create(['ad_size' => '300x250']);
        $lineItem3 = LineItem::factory()->create(['ad_size' => '160x600']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->searchTable('728')
            ->assertCanSeeTableRecords([$lineItem1])
            ->assertCanNotSeeTableRecords([$lineItem2, $lineItem3]);
    }
}