<?php

namespace Tests\Unit;

use App\Models\SavedReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

class SavedReportTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_report_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $report = SavedReport::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $report->user);
    }
}
