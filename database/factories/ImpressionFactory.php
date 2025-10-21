<?php

namespace Database\Factories;

use App\Models\Impression;
use App\Models\LineItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImpressionFactory extends Factory
{
    protected $model = Impression::class;

    public function definition()
    {
        return [
            'line_item_id' => LineItem::factory(),
            'campaign_id' => function (array $attributes) {
                return \App\Models\LineItem::find($attributes['line_item_id'])->campaign_id;
            },
            'date' => $this->faker->unique()->dateTimeBetween('-30 days', 'yesterday')->format('Y-m-d'),
            'impressions' => $this->faker->numberBetween(100, 10000),
            'clicks' => $this->faker->numberBetween(1, 100),
            'revenue' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
