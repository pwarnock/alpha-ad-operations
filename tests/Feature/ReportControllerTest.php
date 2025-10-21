<?php

namespace Tests\Feature;

use App\Models\Advertiser;
use App\Models\Campaign;
use App\Models\Impression;
use App\Models\LineItem;
use App\Models\SavedReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelPdf\Facades\Pdf;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected SavedReport $publicReport;
    protected SavedReport $privateReport;
    protected SavedReport $otherUserReport;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create tenant for testing
        $tenant = \App\Models\Tenant::factory()->create();
        
        $this->user = User::factory()->create(['tenant_id' => $tenant->id]);
        $this->otherUser = User::factory()->create(['tenant_id' => $tenant->id]);
        
        $this->publicReport = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'tenant_id' => $tenant->id,
            'is_public' => true,
            'name' => 'Public Report',
        ]);
        
        $this->privateReport = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'tenant_id' => $tenant->id,
            'is_public' => false,
            'name' => 'Private Report',
        ]);
        
        $this->otherUserReport = SavedReport::factory()->create([
            'user_id' => $this->otherUser->id,
            'tenant_id' => $tenant->id,
            'is_public' => false,
            'name' => 'Other User Report',
        ]);
    }

    #[Test]
    public function user_can_view_own_public_report()
    {
        $response = $this->actingAs($this->user)
            ->get(route('reports.run', $this->publicReport));

        $response->assertSuccessful();
        $response->assertViewIs('reports.show');
        $response->assertViewHas('report', $this->publicReport);
        $response->assertViewHas('data');
    }

    #[Test]
    public function user_can_view_own_private_report()
    {
        $response = $this->actingAs($this->user)
            ->get(route('reports.run', $this->privateReport));

        $response->assertSuccessful();
        $response->assertViewIs('reports.show');
        $response->assertViewHas('report', $this->privateReport);
    }

    #[Test]
    public function user_can_view_other_users_public_report()
    {
        $otherPublicReport = SavedReport::factory()->create([
            'user_id' => $this->otherUser->id,
            'is_public' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('reports.run', $otherPublicReport));

        $response->assertSuccessful();
        $response->assertViewIs('reports.show');
    }

    #[Test]
    public function user_cannot_view_other_users_private_report()
    {
        $response = $this->actingAs($this->user)
            ->get(route('reports.run', $this->otherUserReport));

        $response->assertForbidden();
    }

    #[Test]
    public function guest_cannot_view_any_report()
    {
        $response = $this->get(route('reports.run', $this->publicReport));

        $response->assertRedirectToRoute('login');
    }

    #[Test]
    public function user_can_export_own_public_report_to_pdf()
    {
        Pdf::fake();

        $response = $this->actingAs($this->user)
            ->post(route('reports.export.pdf', $this->publicReport));

        $response->assertSuccessful();
        Pdf::assertDownloaded($this->publicReport->name . '.pdf');
    }

    #[Test]
    public function user_can_export_own_private_report_to_pdf()
    {
        Pdf::fake();

        $response = $this->actingAs($this->user)
            ->post(route('reports.export.pdf', $this->privateReport));

        $response->assertSuccessful();
        Pdf::assertDownloaded($this->privateReport->name . '.pdf');
    }

    #[Test]
    public function user_can_export_own_public_report_to_excel()
    {
        Excel::fake();

        $response = $this->actingAs($this->user)
            ->post(route('reports.export.excel', $this->publicReport));

        $response->assertSuccessful();
        Excel::assertDownloaded($this->publicReport->name . '.xlsx');
    }

    #[Test]
    public function user_can_export_own_private_report_to_excel()
    {
        Excel::fake();

        $response = $this->actingAs($this->user)
            ->post(route('reports.export.excel', $this->privateReport));

        $response->assertSuccessful();
        Excel::assertDownloaded($this->privateReport->name . '.xlsx');
    }

    #[Test]
    public function user_cannot_export_other_users_private_report()
    {
        Pdf::fake();

        $response = $this->actingAs($this->user)
            ->post(route('reports.export.pdf', $this->otherUserReport));

        $response->assertForbidden();
        Pdf::assertNothingDownloaded();
    }

    #[Test]
    public function guest_cannot_export_any_report()
    {
        $response = $this->post(route('reports.export.pdf', $this->publicReport));

        $response->assertRedirectToRoute('login');
    }

    #[Test]
    public function report_data_is_filtered_correctly_by_date_range()
    {
        // Create test data
        $advertiser = Advertiser::factory()->create();
        $campaign = Campaign::factory()->create(['advertiser_id' => $advertiser->id]);
        $lineItem = LineItem::factory()->create(['campaign_id' => $campaign->id]);
        
        // Create impressions outside and inside date range
        Impression::factory()->create([
            'line_item_id' => $lineItem->id,
            'campaign_id' => $campaign->id,
            'date' => '2023-01-01',
            'impressions' => 100,
        ]);
        
        Impression::factory()->create([
            'line_item_id' => $lineItem->id,
            'campaign_id' => $campaign->id,
            'date' => '2023-01-15',
            'impressions' => 200,
        ]);
        
        Impression::factory()->create([
            'line_item_id' => $lineItem->id,
            'campaign_id' => $campaign->id,
            'date' => '2023-02-01',
            'impressions' => 300,
        ]);

        $report = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'configuration' => [
                'date_from' => '2023-01-01',
                'date_to' => '2023-01-31',
                'metrics' => ['impressions'],
                'group_by' => 'day',
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('reports.run', $report));

        $response->assertSuccessful();
        $data = $response->viewData('data');
        
        // Should only include impressions from January
        $this->assertCount(2, $data);
        $this->assertEquals(100, $data[0]['impressions']);
        $this->assertEquals(200, $data[1]['impressions']);
    }

    #[Test]
    public function report_data_is_grouped_correctly_by_advertiser()
    {
        $advertiser1 = Advertiser::factory()->create(['name' => 'Advertiser A']);
        $advertiser2 = Advertiser::factory()->create(['name' => 'Advertiser B']);
        
        $campaign1 = Campaign::factory()->create(['advertiser_id' => $advertiser1->id]);
        $campaign2 = Campaign::factory()->create(['advertiser_id' => $advertiser2->id]);
        
        $lineItem1 = LineItem::factory()->create(['campaign_id' => $campaign1->id]);
        $lineItem2 = LineItem::factory()->create(['campaign_id' => $campaign2->id]);
        
        Impression::factory()->create([
            'line_item_id' => $lineItem1->id,
            'campaign_id' => $campaign1->id,
            'impressions' => 100,
        ]);
        
        Impression::factory()->create([
            'line_item_id' => $lineItem2->id,
            'campaign_id' => $campaign2->id,
            'impressions' => 200,
        ]);

        $report = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'report_type' => 'advertiser_performance',
            'configuration' => [
                'metrics' => ['impressions'],
                'group_by' => 'advertiser',
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('reports.run', $report));

        $response->assertSuccessful();
        $data = $response->viewData('data');
        
        $this->assertCount(2, $data);
        
        $advertiserAData = collect($data)->firstWhere('period', 'Advertiser A');
        $advertiserBData = collect($data)->firstWhere('period', 'Advertiser B');
        
        $this->assertEquals(100, $advertiserAData['impressions']);
        $this->assertEquals(200, $advertiserBData['impressions']);
    }

    #[Test]
    public function report_calculates_derived_metrics_correctly()
    {
        $advertiser = Advertiser::factory()->create();
        $campaign = Campaign::factory()->create(['advertiser_id' => $advertiser->id]);
        $lineItem = LineItem::factory()->create(['campaign_id' => $campaign->id]);
        
        Impression::factory()->create([
            'line_item_id' => $lineItem->id,
            'campaign_id' => $campaign->id,
            'impressions' => 1000,
            'clicks' => 50,
            'revenue' => 100.00,
        ]);

        $report = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'configuration' => [
                'metrics' => ['impressions', 'clicks', 'revenue', 'ctr', 'ecpm', 'cpc'],
                'group_by' => 'day',
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('reports.run', $report));

        $response->assertSuccessful();
        $data = $response->viewData('data');
        
        $this->assertCount(1, $data);
        $row = $data[0];
        
        $this->assertEquals(1000, $row['impressions']);
        $this->assertEquals(50, $row['clicks']);
        $this->assertEquals(100.00, $row['revenue']);
        $this->assertEquals(5.0, $row['ctr']); // 50/1000 * 100
        $this->assertEquals(100.0, $row['ecpm']); // 100/1000 * 1000
        $this->assertEquals(2.0, $row['cpc']); // 100/50
    }
}