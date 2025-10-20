<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Advertiser;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition()
    {
        return [
            'advertiser_id' => Advertiser::factory(),
            'name' => $this->faker->sentence(3),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'budget' => $this->faker->randomFloat(2, 100, 10000),
            'rate' => $this->faker->randomFloat(2, 0.1, 10.0),
            'target_url' => $this->faker->url,
        ];
    }
}
