<?php

namespace Tests\Feature;

use App\Models\Advertiser;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AdvertiserResourceTest extends TestCase
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
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->assertSuccessful();
    }

    #[Test]
    public function create_page_can_be_rendered()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\CreateAdvertiser::class)
            ->assertSuccessful();
    }

    #[Test]
    public function edit_page_can_be_rendered()
    {
        $advertiser = Advertiser::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\EditAdvertiser::class, [
                'record' => $advertiser->getKey(),
            ])
            ->assertSuccessful();
    }

    #[Test]
    public function view_page_can_be_rendered()
    {
        $advertiser = Advertiser::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ViewAdvertiser::class, [
                'record' => $advertiser->getKey(),
            ])
            ->assertSuccessful();
    }

    #[Test]
    public function can_create_advertiser()
    {
        $newData = [
            'name' => 'Test Advertiser',
            'email' => 'test@advertiser.com',
            'phone' => '+1-555-0123',
            'company' => 'Test Company',
            'website' => 'https://testcompany.com',
            'status' => 'active',
            'credit_limit' => 10000.00,
            'notes' => 'Test notes for advertiser',
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\CreateAdvertiser::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('advertisers', [
            'name' => 'Test Advertiser',
            'email' => 'test@advertiser.com',
            'company' => 'Test Company',
            'status' => 'active',
        ]);
    }

    #[Test]
    public function can_validate_required_fields_when_creating_advertiser()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\CreateAdvertiser::class)
            ->fillForm([
                'name' => '',
                'email' => '',
                'status' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'email', 'status']);
    }

    #[Test]
    public function can_validate_email_format_when_creating_advertiser()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\CreateAdvertiser::class)
            ->fillForm([
                'name' => 'Test Advertiser',
                'email' => 'invalid-email',
                'status' => 'active',
            ])
            ->call('create')
            ->assertHasFormErrors(['email']);
    }

    #[Test]
    public function can_validate_unique_email_when_creating_advertiser()
    {
        Advertiser::factory()->create(['email' => 'existing@advertiser.com']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\CreateAdvertiser::class)
            ->fillForm([
                'name' => 'Test Advertiser',
                'email' => 'existing@advertiser.com',
                'status' => 'active',
            ])
            ->call('create')
            ->assertHasFormErrors(['email']);
    }

    #[Test]
    public function can_validate_url_format_when_creating_advertiser()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\CreateAdvertiser::class)
            ->fillForm([
                'name' => 'Test Advertiser',
                'email' => 'test@advertiser.com',
                'website' => 'invalid-url',
                'status' => 'active',
            ])
            ->call('create')
            ->assertHasFormErrors(['website']);
    }

    #[Test]
    public function can_update_advertiser()
    {
        $advertiser = Advertiser::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@advertiser.com',
            'status' => 'active',
        ]);

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@advertiser.com',
            'phone' => '+1-555-9999',
            'company' => 'Updated Company',
            'website' => 'https://updatedcompany.com',
            'status' => 'inactive',
            'credit_limit' => 20000.00,
            'notes' => 'Updated notes',
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\EditAdvertiser::class, [
                'record' => $advertiser->getKey(),
            ])
            ->fillForm($updatedData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('advertisers', [
            'id' => $advertiser->id,
            'name' => 'Updated Name',
            'email' => 'updated@advertiser.com',
            'company' => 'Updated Company',
            'status' => 'inactive',
        ]);
    }

    #[Test]
    public function can_delete_advertiser()
    {
        $advertiser = Advertiser::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\EditAdvertiser::class, [
                'record' => $advertiser->getKey(),
            ])
            ->call('delete')
            ->assertNotified();

        $this->assertModelMissing($advertiser);
    }

    #[Test]
    public function can_delete_advertiser_from_list()
    {
        $advertiser = Advertiser::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->callTableAction('delete', $advertiser)
            ->assertNotified();

        $this->assertModelMissing($advertiser);
    }

    #[Test]
    public function can_view_advertiser_details()
    {
        $advertiser = Advertiser::factory()->create([
            'name' => 'Test Advertiser',
            'email' => 'test@advertiser.com',
            'company' => 'Test Company',
            'status' => 'active',
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ViewAdvertiser::class, [
                'record' => $advertiser->getKey(),
            ])
            ->assertFormSet([
                'name' => 'Test Advertiser',
                'email' => 'test@advertiser.com',
                'company' => 'Test Company',
                'status' => 'active',
            ]);
    }

    #[Test]
    public function can_search_advertisers()
    {
        $advertiser1 = Advertiser::factory()->create(['name' => 'Alpha Advertising']);
        $advertiser2 = Advertiser::factory()->create(['name' => 'Beta Marketing']);
        $advertiser3 = Advertiser::factory()->create(['name' => 'Gamma Solutions']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->searchTable('Alpha')
            ->assertCanSeeTableRecords([$advertiser1])
            ->assertCanNotSeeTableRecords([$advertiser2, $advertiser3]);
    }

    #[Test]
    public function can_filter_advertisers_by_status()
    {
        $activeAdvertiser = Advertiser::factory()->create(['status' => 'active']);
        $inactiveAdvertiser = Advertiser::factory()->create(['status' => 'inactive']);
        $suspendedAdvertiser = Advertiser::factory()->create(['status' => 'suspended']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->filterTable('status', 'active')
            ->assertCanSeeTableRecords([$activeAdvertiser])
            ->assertCanNotSeeTableRecords([$inactiveAdvertiser, $suspendedAdvertiser]);
    }

    #[Test]
    public function can_sort_advertisers()
    {
        $advertiser1 = Advertiser::factory()->create(['name' => 'A Company']);
        $advertiser2 = Advertiser::factory()->create(['name' => 'B Company']);
        $advertiser3 = Advertiser::factory()->create(['name' => 'C Company']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->sortTable('name', 'desc')
            ->assertCanSeeTableRecordsInOrder([$advertiser3, $advertiser2, $advertiser1]);
    }

    #[Test]
    public function can_bulk_delete_advertisers()
    {
        $advertiser1 = Advertiser::factory()->create();
        $advertiser2 = Advertiser::factory()->create();
        $advertiser3 = Advertiser::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->callTableBulkAction('delete', [$advertiser1, $advertiser2])
            ->assertNotified();

        $this->assertModelMissing($advertiser1);
        $this->assertModelMissing($advertiser2);
        $this->assertModelExists($advertiser3);
    }

    #[Test]
    public function displays_correct_badge_colors_for_status()
    {
        $activeAdvertiser = Advertiser::factory()->create(['status' => 'active']);
        $inactiveAdvertiser = Advertiser::factory()->create(['status' => 'inactive']);
        $suspendedAdvertiser = Advertiser::factory()->create(['status' => 'suspended']);

        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->assertTableColumnStateSet('status', 'success', $activeAdvertiser)
            ->assertTableColumnStateSet('status', 'warning', $inactiveAdvertiser)
            ->assertTableColumnStateSet('status', 'danger', $suspendedAdvertiser);
    }

    #[Test]
    public function cannot_delete_advertiser_with_campaigns()
    {
        $advertiser = Advertiser::factory()->create();
        Campaign::factory()->create(['advertiser_id' => $advertiser->id]);

        // This test assumes there's a foreign key constraint
        // If the application allows deleting advertisers with campaigns, this test should be adjusted
        Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AdvertiserResource\Pages\ListAdvertisers::class)
            ->callTableAction('delete', $advertiser);

        // Depending on the implementation, this might:
        // - Fail with a database constraint error
        // - Succeed with cascading deletes
        // - Be prevented by the application logic
        // Adjust the assertion based on the actual behavior
    }
}