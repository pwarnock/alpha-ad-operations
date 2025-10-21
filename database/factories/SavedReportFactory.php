<?php

namespace Database\Factories;

use App\Models\SavedReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavedReportFactory extends Factory
{
    protected $model = SavedReport::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'tenant_id' => 1,
            'name' => $this->faker->sentence,
            'report_type' => $this->faker->randomElement(['advertiser_performance', 'inventory', 'campaign_delivery', 'revenue']),
            'configuration' => [
                'filters' => [
                    'date_from' => $this->faker->date(),
                    'date_to' => $this->faker->date(),
                ],
                'metrics' => $this->faker->randomElements(['impressions', 'clicks', 'ctr', 'revenue', 'ecpm', 'cpc'], $this->faker->numberBetween(1, 3)),
                'group_by' => ['date'],
            ],
            'is_public' => $this->faker->boolean,
        ];
    }
}
