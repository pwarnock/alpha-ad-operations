<?php

namespace Tests\Feature;

use App\Models\SavedReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ReportManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_create_a_saved_report()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\CreateSavedReport::class)
            ->fillForm([
                'name' => 'My Test Report',
                'report_type' => 'advertiser_performance',
                'is_public' => true,
                'configuration.date_from' => '2023-01-01',
                'configuration.date_to' => '2023-01-31',
                'configuration.metrics' => ['impressions', 'clicks'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('saved_reports', [
            'name' => 'My Test Report',
            'user_id' => $user->id,
        ]);
    }

    // #[Test]
    // public function a_user_can_view_a_saved_report()
    // {
    //     $user = User::factory()->create();
    //     $report = SavedReport::factory()->create(['user_id' => $user->id]);

    //     $this->actingAs($user)
    //         ->get(route('filament.admin.resources.saved-reports.view', $report))
    //         ->assertSuccessful();
    // }

    // #[Test]
    // public function a_user_can_update_a_saved_report()
    // {
    //     $user = User::factory()->create();
    //     $report = SavedReport::factory()->create(['user_id' => $user->id]);

    //     $this->actingAs($user)
    //         ->livewire(\App\Filament\Resources\SavedReportResource\Pages\EditSavedReport::class, [
    //             'record' => $report->getKey(),
    //         ])
    //         ->fillForm([
    //             'name' => 'Updated Report Name',
    //             'report_type' => 'inventory',
    //             'is_public' => false,
    //             'configuration.date_from' => '2023-02-01',
    //             'configuration.date_to' => '2023-02-28',
    //             'configuration.metrics' => ['revenue'],
    //         ])
    //         ->call('save')
    //         ->assertHasNoFormErrors();

    //     $this->assertDatabaseHas('saved_reports', [
    //         'id' => $report->id,
    //         'name' => 'Updated Report Name',
    //         'is_public' => false,
    //     ]);
    // }

    // #[Test]
    // public function a_user_can_delete_a_saved_report()
    // {
    //     $user = User::factory()->create();
    //     $report = SavedReport::factory()->create(['user_id' => $user->id]);

    //     $this->actingAs($user);
    //     $report->delete();

    //     $this->assertDatabaseMissing('saved_reports', ['id' => $report->id]);
    // }
}
