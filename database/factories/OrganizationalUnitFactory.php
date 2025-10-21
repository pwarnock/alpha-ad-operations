<?php

namespace Database\Factories;

use App\Models\OrganizationalUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationalUnit>
 */
class OrganizationalUnitFactory extends Factory
{
    protected $model = OrganizationalUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->department(),
            'slug' => $this->faker->unique()->slug(),
            'organization_id' => \App\Models\Organization::factory(),
            'parent_id' => null,
            'is_active' => true,
        ];
    }
}