<?php

namespace Database\Factories;

use App\Models\ProneArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subdistrict>
 */
class SubdistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->city(),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->text(),
            'prone_area_id' => ProneArea::factory(),
        ];
    }
}
