<?php

namespace Database\Factories;

use App\Enums\ProneZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProneArea>
 */
class ProneAreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->state(),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->text(),
            'zone' => fake()->randomElement(ProneZone::class),
        ];
    }
}
