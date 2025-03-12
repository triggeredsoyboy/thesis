<?php

namespace Database\Factories;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->text(),
            'status' => fake()->randomElement(CategoryStatus::class),
        ];
    }
}
