<?php

namespace Database\Factories;

use App\Models\LineItem;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

class LineItemFactory extends Factory
{
    protected $model = LineItem::class;

    public function definition()
    {
        return [
            'campaign_id' => Campaign::factory(),
            'name' => $this->faker->sentence(4),
            'ad_size' => $this->faker->randomElement(['728x90', '300x250', '160x600']),
            'rate' => $this->faker->randomFloat(2, 0.5, 5.0),
            'impressions_goal' => $this->faker->numberBetween(1000, 100000),
            'budget' => $this->faker->randomFloat(2, 100, 10000),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'ad_zone' => $this->faker->word,
        ];
    }
}
