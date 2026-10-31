<?php

namespace Database\Factories;

use App\Models\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Instructor>
 */
class InstructorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->firstName();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'tagline' => fake()->sentence(4),
            'bio' => fake()->paragraph(),
            'specialties' => ['Mat'],
            'figure' => fake()->numberBetween(0, 5),
            'is_active' => true,
            'sort' => 0,
        ];
    }
}
