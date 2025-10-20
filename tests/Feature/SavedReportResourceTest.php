<?php

namespace Tests\Feature;

use App\Models\SavedReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SavedReportResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    #[Test]
    public function list_page_can_be_rendered()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->assertSuccessful();
    }

    #[Test]
    public function create_page_can_be_rendered()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\CreateSavedReport::class)
            ->assertSuccessful();
    }

    #[Test]
    public function edit_page_can_be_rendered()
    {
        $report = SavedReport::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\EditSavedReport::class, [
                'record' => $report->getKey(),
            ])
            ->assertSuccessful();
    }

    #[Test]
    public function view_page_can_be_rendered()
    {
        $report = SavedReport::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ViewSavedReport::class, [
                'record' => $report->getKey(),
            ])
            ->assertSuccessful();
    }

    #[Test]
    public function can_create_saved_report()
    {
        $newData = [
            'name' => 'My Test Report',
            'report_type' => 'advertiser_performance',
            'is_public' => true,
            'configuration' => [
                'date_from' => '2023-01-01',
                'date_to' => '2023-01-31',
                'metrics' => ['impressions', 'clicks'],
                'group_by' => 'day',
                'advertisers' => [1, 2],
            ],
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\CreateSavedReport::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('saved_reports', [
            'name' => 'My Test Report',
            'user_id' => $this->user->id,
            'report_type' => 'advertiser_performance',
            'is_public' => true,
        ]);
    }

    #[Test]
    public function can_validate_required_fields_when_creating_saved_report()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\CreateSavedReport::class)
            ->fillForm([
                'name' => '',
                'report_type' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'report_type']);
    }

    #[Test]
    public function can_create_different_report_types()
    {
        $reportTypes = ['advertiser_performance', 'inventory', 'campaign_delivery', 'revenue'];

        foreach ($reportTypes as $reportType) {
            $newData = [
                'name' => "Test {$reportType} Report",
                'report_type' => $reportType,
                'is_public' => false,
                'configuration' => [
                    'date_from' => '2023-01-01',
                    'date_to' => '2023-01-31',
                    'metrics' => ['impressions'],
                ],
            ];

            Livewire::actingAs($this->user)
                ->test(\App\Filament\Resources\SavedReportResource\Pages\CreateSavedReport::class)
                ->fillForm($newData)
                ->call('create')
                ->assertHasNoFormErrors();

            $this->assertDatabaseHas('saved_reports', [
                'name' => "Test {$reportType} Report",
                'report_type' => $reportType,
                'user_id' => $this->user->id,
            ]);
        }
    }

    #[Test]
    public function can_update_saved_report()
    {
        $report = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Report',
            'report_type' => 'advertiser_performance',
            'is_public' => false,
        ]);

        $updatedData = [
            'name' => 'Updated Report Name',
            'report_type' => 'inventory',
            'is_public' => true,
            'configuration' => [
                'date_from' => '2023-02-01',
                'date_to' => '2023-02-28',
                'metrics' => ['revenue', 'impressions'],
                'group_by' => 'week',
                'ad_sizes' => ['300x250', '728x90'],
            ],
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\EditSavedReport::class, [
                'record' => $report->getKey(),
            ])
            ->fillForm($updatedData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('saved_reports', [
            'id' => $report->id,
            'name' => 'Updated Report Name',
            'report_type' => 'inventory',
            'is_public' => true,
        ]);
    }

    #[Test]
    public function can_delete_saved_report()
    {
        $report = SavedReport::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\EditSavedReport::class, [
                'record' => $report->getKey(),
            ])
            ->call('delete')
            ->assertNotified();

        $this->assertModelMissing($report);
    }

    #[Test]
    public function can_delete_saved_report_from_list()
    {
        $report = SavedReport::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->callTableAction('delete', $report)
            ->assertNotified();

        $this->assertModelMissing($report);
    }

    #[Test]
    public function can_view_saved_report_details()
    {
        $report = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Report',
            'report_type' => 'campaign_delivery',
            'is_public' => true,
            'configuration' => [
                'date_from' => '2023-01-01',
                'date_to' => '2023-01-31',
                'metrics' => ['impressions', 'clicks'],
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ViewSavedReport::class, [
                'record' => $report->getKey(),
            ])
            ->assertFormSet([
                'name' => 'Test Report',
                'report_type' => 'campaign_delivery',
                'is_public' => true,
            ]);
    }

    #[Test]
    public function can_search_saved_reports()
    {
        $report1 = SavedReport::factory()->create(['user_id' => $this->user->id, 'name' => 'Alpha Report']);
        $report2 = SavedReport::factory()->create(['user_id' => $this->user->id, 'name' => 'Beta Report']);
        $report3 = SavedReport::factory()->create(['user_id' => $this->user->id, 'name' => 'Gamma Report']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->searchTable('Alpha')
            ->assertCanSeeTableRecords([$report1])
            ->assertCanNotSeeTableRecords([$report2, $report3]);
    }

    #[Test]
    public function can_filter_saved_reports_by_type()
    {
        $advertiserReport = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'report_type' => 'advertiser_performance',
        ]);
        $inventoryReport = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'report_type' => 'inventory',
        ]);
        $campaignReport = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'report_type' => 'campaign_delivery',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->filterTable('report_type', 'inventory')
            ->assertCanSeeTableRecords([$inventoryReport])
            ->assertCanNotSeeTableRecords([$advertiserReport, $campaignReport]);
    }

    #[Test]
    public function can_sort_saved_reports()
    {
        $report1 = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'A Report',
            'created_at' => now()->subDays(3),
        ]);
        $report2 = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'B Report',
            'created_at' => now()->subDays(1),
        ]);
        $report3 = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'C Report',
            'created_at' => now()->subDays(2),
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->sortTable('created_at', 'desc')
            ->assertCanSeeTableRecordsInOrder([$report2, $report3, $report1]);
    }

    #[Test]
    public function can_bulk_delete_saved_reports()
    {
        $report1 = SavedReport::factory()->create(['user_id' => $this->user->id]);
        $report2 = SavedReport::factory()->create(['user_id' => $this->user->id]);
        $report3 = SavedReport::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->callTableBulkAction('delete', [$report1, $report2])
            ->assertNotified();

        $this->assertModelMissing($report1);
        $this->assertModelMissing($report2);
        $this->assertModelExists($report3);
    }

    #[Test]
    public function user_can_only_see_own_reports_in_list()
    {
        $otherUser = User::factory()->create();
        
        $userReport = SavedReport::factory()->create(['user_id' => $this->user->id]);
        $otherUserReport = SavedReport::factory()->create(['user_id' => $otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->assertCanSeeTableRecords([$userReport])
            ->assertCanNotSeeTableRecords([$otherUserReport]);
    }

    #[Test]
    public function user_cannot_edit_other_users_reports()
    {
        $otherUser = User::factory()->create();
        $otherUserReport = SavedReport::factory()->create(['user_id' => $otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\EditSavedReport::class, [
                'record' => $otherUserReport->getKey(),
            ])
            ->assertForbidden();
    }

    #[Test]
    public function user_cannot_delete_other_users_reports()
    {
        $otherUser = User::factory()->create();
        $otherUserReport = SavedReport::factory()->create(['user_id' => $otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->assertTableActionHidden('delete', $otherUserReport);
    }

    #[Test]
    public function can_create_report_with_all_metric_options()
    {
        $allMetrics = ['impressions', 'clicks', 'ctr', 'revenue', 'ecpm', 'cpc'];

        $newData = [
            'name' => 'All Metrics Report',
            'report_type' => 'advertiser_performance',
            'is_public' => false,
            'configuration' => [
                'date_from' => '2023-01-01',
                'date_to' => '2023-01-31',
                'metrics' => $allMetrics,
                'group_by' => 'advertiser',
            ],
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\CreateSavedReport::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('saved_reports', [
            'name' => 'All Metrics Report',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function can_create_report_with_different_group_by_options()
    {
        $groupByOptions = ['day', 'week', 'month', 'advertiser', 'campaign'];

        foreach ($groupByOptions as $groupBy) {
            $newData = [
                'name' => "Group by {$groupBy} Report",
                'report_type' => 'advertiser_performance',
                'is_public' => false,
                'configuration' => [
                    'date_from' => '2023-01-01',
                    'date_to' => '2023-01-31',
                    'metrics' => ['impressions'],
                    'group_by' => $groupBy,
                ],
            ];

            Livewire::actingAs($this->user)
                ->test(\App\Filament\Resources\SavedReportResource\Pages\CreateSavedReport::class)
                ->fillForm($newData)
                ->call('create')
                ->assertHasNoFormErrors();

            $this->assertDatabaseHas('saved_reports', [
                'name' => "Group by {$groupBy} Report",
                'user_id' => $this->user->id,
            ]);
        }
    }

    #[Test]
    public function can_toggle_public_visibility()
    {
        $report = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'is_public' => false,
        ]);

        // Make public
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\EditSavedReport::class, [
                'record' => $report->getKey(),
            ])
            ->fillForm(['is_public' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('saved_reports', [
            'id' => $report->id,
            'is_public' => true,
        ]);

        // Make private again
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\EditSavedReport::class, [
                'record' => $report->getKey(),
            ])
            ->fillForm(['is_public' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('saved_reports', [
            'id' => $report->id,
            'is_public' => false,
        ]);
    }
}