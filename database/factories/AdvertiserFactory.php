<?php

namespace Database\Factories;

use App\Models\Advertiser;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdvertiserFactory extends Factory
{
    protected $model = Advertiser::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
