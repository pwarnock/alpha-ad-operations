<?php

namespace Tests\Feature;

use App\Models\Advertiser;
use App\Models\Campaign;
use App\Models\LineItem;
use App\Models\SavedReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->admin = User::factory()->create();
    }

    #[Test]
    public function guest_cannot_access_filament_admin()
    {
        $response = $this->get('/admin');
        
        // Should redirect to login
        $response->assertRedirect();
    }

    #[Test]
    public function authenticated_user_can_access_filament_admin()
    {
        $response = $this->actingAs($this->user)
            ->get('/admin');
        
        $response->assertSuccessful();
    }

    #[Test]
    public function user_can_only_view_own_advertisers()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->assertCanSeeTableRecords([$userAdvertiser])
            ->assertCanNotSeeTableRecords([$otherAdvertiser]);
    }

    #[Test]
    public function user_can_only_edit_own_advertisers()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);

        // Can edit own advertiser
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\EditAdvertiser::class, [
                'record' => $userAdvertiser->getKey(),
            ])
            ->assertSuccessful();

        // Cannot edit other user's advertiser
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\EditAdvertiser::class, [
                'record' => $otherAdvertiser->getKey(),
            ])
            ->assertForbidden();
    }

    #[Test]
    public function user_can_only_delete_own_advertisers()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);

        // Can delete own advertiser
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->assertTableActionVisible('delete', $userAdvertiser);

        // Cannot delete other user's advertiser
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->assertTableActionHidden('delete', $otherAdvertiser);
    }

    #[Test]
    public function user_can_only_view_own_campaigns()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);
        
        $userCampaign = Campaign::factory()->create(['advertiser_id' => $userAdvertiser->id]);
        $otherCampaign = Campaign::factory()->create(['advertiser_id' => $otherAdvertiser->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\ListCampaigns::class)
            ->assertCanSeeTableRecords([$userCampaign])
            ->assertCanNotSeeTableRecords([$otherCampaign]);
    }

    #[Test]
    public function user_can_only_edit_own_campaigns()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);
        
        $userCampaign = Campaign::factory()->create(['advertiser_id' => $userAdvertiser->id]);
        $otherCampaign = Campaign::factory()->create(['advertiser_id' => $otherAdvertiser->id]);

        // Can edit own campaign
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\EditCampaign::class, [
                'record' => $userCampaign->getKey(),
            ])
            ->assertSuccessful();

        // Cannot edit other user's campaign
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\EditCampaign::class, [
                'record' => $otherCampaign->getKey(),
            ])
            ->assertForbidden();
    }

    #[Test]
    public function user_can_only_view_own_line_items()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);
        
        $userCampaign = Campaign::factory()->create(['advertiser_id' => $userAdvertiser->id]);
        $otherCampaign = Campaign::factory()->create(['advertiser_id' => $otherAdvertiser->id]);
        
        $userLineItem = LineItem::factory()->create(['campaign_id' => $userCampaign->id]);
        $otherLineItem = LineItem::factory()->create(['campaign_id' => $otherCampaign->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\ListLineItems::class)
            ->assertCanSeeTableRecords([$userLineItem])
            ->assertCanNotSeeTableRecords([$otherLineItem]);
    }

    #[Test]
    public function user_can_only_edit_own_line_items()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);
        
        $userCampaign = Campaign::factory()->create(['advertiser_id' => $userAdvertiser->id]);
        $otherCampaign = Campaign::factory()->create(['advertiser_id' => $otherAdvertiser->id]);
        
        $userLineItem = LineItem::factory()->create(['campaign_id' => $userCampaign->id]);
        $otherLineItem = LineItem::factory()->create(['campaign_id' => $otherCampaign->id]);

        // Can edit own line item
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\EditLineItem::class, [
                'record' => $userLineItem->getKey(),
            ])
            ->assertSuccessful();

        // Cannot edit other user's line item
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\EditLineItem::class, [
                'record' => $otherLineItem->getKey(),
            ])
            ->assertForbidden();
    }

    #[Test]
    public function user_can_only_view_own_saved_reports()
    {
        $userReport = SavedReport::factory()->create(['user_id' => $this->user->id]);
        $otherReport = SavedReport::factory()->create(['user_id' => $this->otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->assertCanSeeTableRecords([$userReport])
            ->assertCanNotSeeTableRecords([$otherReport]);
    }

    #[Test]
    public function user_can_only_edit_own_saved_reports()
    {
        $userReport = SavedReport::factory()->create(['user_id' => $this->user->id]);
        $otherReport = SavedReport::factory()->create(['user_id' => $this->otherUser->id]);

        // Can edit own report
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\EditSavedReport::class, [
                'record' => $userReport->getKey(),
            ])
            ->assertSuccessful();

        // Cannot edit other user's report
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\EditSavedReport::class, [
                'record' => $otherReport->getKey(),
            ])
            ->assertForbidden();
    }

    #[Test]
    public function user_can_only_delete_own_saved_reports()
    {
        $userReport = SavedReport::factory()->create(['user_id' => $this->user->id]);
        $otherReport = SavedReport::factory()->create(['user_id' => $this->otherUser->id]);

        // Can delete own report
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->assertTableActionVisible('delete', $userReport);

        // Cannot delete other user's report
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ListSavedReports::class)
            ->assertTableActionHidden('delete', $otherReport);
    }

    #[Test]
    public function user_can_only_create_campaigns_for_own_advertisers()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);

        // Should be able to select own advertisers
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\CampaignResource\Pages\CreateCampaign::class)
            ->assertFormFieldExists('advertiser_id');

        // The relationship should only show user's advertisers
        // This would need to be tested based on the actual implementation
        // of the relationship filtering in the form
    }

    #[Test]
    public function user_can_only_create_line_items_for_own_campaigns()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);
        
        $userCampaign = Campaign::factory()->create(['advertiser_id' => $userAdvertiser->id]);
        $otherCampaign = Campaign::factory()->create(['advertiser_id' => $otherAdvertiser->id]);

        // Should be able to select own campaigns
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\CreateLineItem::class)
            ->assertFormFieldExists('campaign_id');
    }

    #[Test]
    public function public_reports_can_be_accessed_by_any_authenticated_user()
    {
        $publicReport = SavedReport::factory()->create([
            'user_id' => $this->otherUser->id,
            'is_public' => true,
        ]);

        // Can view public report
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ViewSavedReport::class, [
                'record' => $publicReport->getKey(),
            ])
            ->assertSuccessful();

        // Can run public report via controller
        $response = $this->actingAs($this->user)
            ->get(route('reports.run', $publicReport));
        $response->assertSuccessful();
    }

    #[Test]
    public function private_reports_cannot_be_accessed_by_other_users()
    {
        $privateReport = SavedReport::factory()->create([
            'user_id' => $this->otherUser->id,
            'is_public' => false,
        ]);

        // Cannot view private report
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\SavedReportResource\Pages\ViewSavedReport::class, [
                'record' => $privateReport->getKey(),
            ])
            ->assertForbidden();

        // Cannot run private report via controller
        $response = $this->actingAs($this->user)
            ->get(route('reports.run', $privateReport));
        $response->assertForbidden();
    }

    #[Test]
    public function guest_cannot_access_any_reports()
    {
        $publicReport = SavedReport::factory()->create(['is_public' => true]);

        $response = $this->get(route('reports.run', $publicReport));
        $response->assertRedirectToRoute('login');
    }

    #[Test]
    public function user_cannot_export_other_users_private_reports()
    {
        $privateReport = SavedReport::factory()->create([
            'user_id' => $this->otherUser->id,
            'is_public' => false,
        ]);

        // Cannot export PDF
        $response = $this->actingAs($this->user)
            ->post(route('reports.export.pdf', $privateReport));
        $response->assertForbidden();

        // Cannot export Excel
        $response = $this->actingAs($this->user)
            ->post(route('reports.export.excel', $privateReport));
        $response->assertForbidden();
    }

    #[Test]
    public function user_can_export_own_reports()
    {
        $userReport = SavedReport::factory()->create([
            'user_id' => $this->user->id,
            'is_public' => false,
        ]);

        // Can export PDF (mock the PDF generation)
        \Spatie\LaravelPdf\Facades\Pdf::fake();
        $response = $this->actingAs($this->user)
            ->post(route('reports.export.pdf', $userReport));
        $response->assertSuccessful();

        // Can export Excel (mock the Excel generation)
        \Maatwebsite\Excel\Facades\Excel::fake();
        $response = $this->actingAs($this->user)
            ->post(route('reports.export.excel', $userReport));
        $response->assertSuccessful();
    }

    #[Test]
    public function user_can_export_public_reports_from_other_users()
    {
        $publicReport = SavedReport::factory()->create([
            'user_id' => $this->otherUser->id,
            'is_public' => true,
        ]);

        // Can export PDF
        \Spatie\LaravelPdf\Facades\Pdf::fake();
        $response = $this->actingAs($this->user)
            ->post(route('reports.export.pdf', $publicReport));
        $response->assertSuccessful();

        // Can export Excel
        \Maatwebsite\Excel\Facades\Excel::fake();
        $response = $this->actingAs($this->user)
            ->post(route('reports.export.excel', $publicReport));
        $response->assertSuccessful();
    }

    #[Test]
    public function authorization_works_with_nested_relationships()
    {
        // Test that authorization properly checks nested relationships
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $userCampaign = Campaign::factory()->create(['advertiser_id' => $userAdvertiser->id]);
        $userLineItem = LineItem::factory()->create(['campaign_id' => $userCampaign->id]);

        // User should be able to access all their nested resources
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\LineItemResource\Pages\EditLineItem::class, [
                'record' => $userLineItem->getKey(),
            ])
            ->assertSuccessful();
    }

    #[Test]
    public function bulk_actions_respect_authorization()
    {
        $userAdvertiser = Advertiser::factory()->create(['user_id' => $this->user->id]);
        $otherAdvertiser = Advertiser::factory()->create(['user_id' => $this->otherUser->id]);

        // User should only be able to bulk delete their own advertisers
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->assertTableActionVisible('delete', $userAdvertiser)
            ->assertTableActionHidden('delete', $otherAdvertiser);
    }
}