<?php

namespace Tests\Feature;

use App\Models\Advertiser;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CampaignResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Advertiser $advertiser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->advertiser = Advertiser::factory()->create();
    }

    #[Test]
    public function list_page_can_be_rendered()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->assertSuccessful();
    }

    #[Test]
    public function create_page_can_be_rendered()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\CreateCampaign::class)
            ->assertSuccessful();
    }

    #[Test]
    public function edit_page_can_be_rendered()
    {
        $campaign = Campaign::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\EditCampaign::class, [
                'record' => $campaign->getKey(),
            ])
            ->assertSuccessful();
    }

    #[Test]
    public function view_page_can_be_rendered()
    {
        $campaign = Campaign::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ViewCampaign::class, [
                'record' => $campaign->getKey(),
            ])
            ->assertSuccessful();
    }

    #[Test]
    public function can_create_campaign()
    {
        $newData = [
            'advertiser_id' => $this->advertiser->id,
            'name' => 'Test Campaign',
            'description' => 'Test campaign description',
            'status' => 'draft',
            'budget' => 10000.00,
            'pricing_model' => 'CPM',
            'rate' => 5.50,
            'start_date' => '2023-01-01',
            'end_date' => '2023-12-31',
            'target_url' => 'https://example.com/landing-page',
            'targeting' => [
                'geo' => 'US,CA',
                'age' => '25-45',
            ],
            'notes' => 'Campaign notes',
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\CreateCampaign::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('campaigns', [
            'name' => 'Test Campaign',
            'advertiser_id' => $this->advertiser->id,
            'status' => 'draft',
            'budget' => 10000.00,
            'pricing_model' => 'CPM',
            'rate' => 5.50,
        ]);
    }

    #[Test]
    public function can_validate_required_fields_when_creating_campaign()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\CreateCampaign::class)
            ->fillForm([
                'advertiser_id' => '',
                'name' => '',
                'status' => '',
                'budget' => '',
                'pricing_model' => '',
                'rate' => '',
                'start_date' => '',
                'end_date' => '',
                'target_url' => '',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'advertiser_id',
                'name',
                'status',
                'budget',
                'pricing_model',
                'rate',
                'start_date',
                'end_date',
                'target_url',
            ]);
    }

    #[Test]
    public function can_validate_end_date_after_start_date()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\CreateCampaign::class)
            ->fillForm([
                'advertiser_id' => $this->advertiser->id,
                'name' => 'Test Campaign',
                'status' => 'draft',
                'budget' => 1000,
                'pricing_model' => 'CPM',
                'rate' => 5.00,
                'start_date' => '2023-12-31',
                'end_date' => '2023-01-01', // Before start date
                'target_url' => 'https://example.com',
            ])
            ->call('create')
            ->assertHasFormErrors(['end_date']);
    }

    #[Test]
    public function can_validate_target_url_format()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\CreateCampaign::class)
            ->fillForm([
                'advertiser_id' => $this->advertiser->id,
                'name' => 'Test Campaign',
                'status' => 'draft',
                'budget' => 1000,
                'pricing_model' => 'CPM',
                'rate' => 5.00,
                'start_date' => '2023-01-01',
                'end_date' => '2023-12-31',
                'target_url' => 'invalid-url',
            ])
            ->call('create')
            ->assertHasFormErrors(['target_url']);
    }

    #[Test]
    public function can_update_campaign()
    {
        $campaign = Campaign::factory()->create([
            'name' => 'Original Campaign',
            'status' => 'draft',
            'budget' => 5000.00,
        ]);

        $updatedData = [
            'advertiser_id' => $this->advertiser->id,
            'name' => 'Updated Campaign',
            'description' => 'Updated description',
            'status' => 'active',
            'budget' => 15000.00,
            'pricing_model' => 'CPC',
            'rate' => 2.50,
            'start_date' => '2023-02-01',
            'end_date' => '2023-11-30',
            'target_url' => 'https://updated-example.com',
            'notes' => 'Updated notes',
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\EditCampaign::class, [
                'record' => $campaign->getKey(),
            ])
            ->fillForm($updatedData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'name' => 'Updated Campaign',
            'status' => 'active',
            'budget' => 15000.00,
            'pricing_model' => 'CPC',
            'rate' => 2.50,
        ]);
    }

    #[Test]
    public function can_delete_campaign()
    {
        $campaign = Campaign::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\EditCampaign::class, [
                'record' => $campaign->getKey(),
            ])
            ->call('delete')
            ->assertNotified();

        $this->assertModelMissing($campaign);
    }

    #[Test]
    public function can_delete_campaign_from_list()
    {
        $campaign = Campaign::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->callTableAction('delete', $campaign)
            ->assertNotified();

        $this->assertModelMissing($campaign);
    }

    #[Test]
    public function can_view_campaign_details()
    {
        $campaign = Campaign::factory()->create([
            'name' => 'Test Campaign',
            'advertiser_id' => $this->advertiser->id,
            'status' => 'active',
            'budget' => 10000.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ViewCampaign::class, [
                'record' => $campaign->getKey(),
            ])
            ->assertFormSet([
                'name' => 'Test Campaign',
                'advertiser_id' => $this->advertiser->id,
                'status' => 'active',
                'budget' => 10000.00,
            ]);
    }

    #[Test]
    public function can_search_campaigns()
    {
        $campaign1 = Campaign::factory()->create(['name' => 'Alpha Campaign']);
        $campaign2 = Campaign::factory()->create(['name' => 'Beta Campaign']);
        $campaign3 = Campaign::factory()->create(['name' => 'Gamma Campaign']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->searchTable('Alpha')
            ->assertCanSeeTableRecords([$campaign1])
            ->assertCanNotSeeTableRecords([$campaign2, $campaign3]);
    }

    #[Test]
    public function can_filter_campaigns_by_status()
    {
        $draftCampaign = Campaign::factory()->create(['status' => 'draft']);
        $activeCampaign = Campaign::factory()->create(['status' => 'active']);
        $pausedCampaign = Campaign::factory()->create(['status' => 'paused']);
        $completedCampaign = Campaign::factory()->create(['status' => 'completed']);
        $cancelledCampaign = Campaign::factory()->create(['status' => 'cancelled']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->filterTable('status', 'active')
            ->assertCanSeeTableRecords([$activeCampaign])
            ->assertCanNotSeeTableRecords([$draftCampaign, $pausedCampaign, $completedCampaign, $cancelledCampaign]);
    }

    #[Test]
    public function can_filter_campaigns_by_advertiser()
    {
        $advertiser1 = Advertiser::factory()->create();
        $advertiser2 = Advertiser::factory()->create();
        
        $campaign1 = Campaign::factory()->create(['advertiser_id' => $advertiser1->id]);
        $campaign2 = Campaign::factory()->create(['advertiser_id' => $advertiser1->id]);
        $campaign3 = Campaign::factory()->create(['advertiser_id' => $advertiser2->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->filterTable('advertiser', $advertiser1->id)
            ->assertCanSeeTableRecords([$campaign1, $campaign2])
            ->assertCanNotSeeTableRecords([$campaign3]);
    }

    #[Test]
    public function can_sort_campaigns()
    {
        $campaign1 = Campaign::factory()->create(['name' => 'A Campaign', 'budget' => 1000]);
        $campaign2 = Campaign::factory()->create(['name' => 'B Campaign', 'budget' => 5000]);
        $campaign3 = Campaign::factory()->create(['name' => 'C Campaign', 'budget' => 3000]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->sortTable('budget', 'desc')
            ->assertCanSeeTableRecordsInOrder([$campaign2, $campaign3, $campaign1]);
    }

    #[Test]
    public function can_bulk_delete_campaigns()
    {
        $campaign1 = Campaign::factory()->create();
        $campaign2 = Campaign::factory()->create();
        $campaign3 = Campaign::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->callTableBulkAction('delete', [$campaign1, $campaign2])
            ->assertNotified();

        $this->assertModelMissing($campaign1);
        $this->assertModelMissing($campaign2);
        $this->assertModelExists($campaign3);
    }

    #[Test]
    public function displays_correct_badge_colors_for_status()
    {
        $draftCampaign = Campaign::factory()->create(['status' => 'draft']);
        $activeCampaign = Campaign::factory()->create(['status' => 'active']);
        $pausedCampaign = Campaign::factory()->create(['status' => 'paused']);
        $completedCampaign = Campaign::factory()->create(['status' => 'completed']);
        $cancelledCampaign = Campaign::factory()->create(['status' => 'cancelled']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->assertTableColumnStateSet('status', 'gray', $draftCampaign)
            ->assertTableColumnStateSet('status', 'success', $activeCampaign)
            ->assertTableColumnStateSet('status', 'warning', $pausedCampaign)
            ->assertTableColumnStateSet('status', 'info', $completedCampaign)
            ->assertTableColumnStateSet('status', 'danger', $cancelledCampaign);
    }

    #[Test]
    public function displays_correct_budget_utilization_colors()
    {
        $lowUtilizationCampaign = Campaign::factory()->create([
            'budget' => 10000,
            'spent' => 2000, // 20%
        ]);
        
        $mediumUtilizationCampaign = Campaign::factory()->create([
            'budget' => 10000,
            'spent' => 8000, // 80%
        ]);
        
        $highUtilizationCampaign = Campaign::factory()->create([
            'budget' => 10000,
            'spent' => 9500, // 95%
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->assertTableColumnStateSet('budget_utilization', 'success', $lowUtilizationCampaign)
            ->assertTableColumnStateSet('budget_utilization', 'warning', $mediumUtilizationCampaign)
            ->assertTableColumnStateSet('budget_utilization', 'danger', $highUtilizationCampaign);
    }

    #[Test]
    public function can_select_advertiser_from_relationship()
    {
        $advertisers = Advertiser::factory()->count(3)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\CreateCampaign::class)
            ->assertFormFieldExists('advertiser_id')
            ->assertFormFieldIsSelectable('advertiser_id');
    }

    #[Test]
    public function spent_field_is_disabled_in_form()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\CreateCampaign::class)
            ->assertFormFieldDisabled('spent');
    }
}