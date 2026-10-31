<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $count = fake()->randomElement([1, 5, 10]);
        $name = "Paket {$count} Kelas";

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'summary' => fake()->sentence(),
            'class_count' => $count,
            'price' => $count * 160_000,
            'validity_days' => 30 * max(1, intdiv($count, 4)),
            'is_intro' => false,
            'is_shareable' => false,
            'is_active' => true,
            'sort' => 0,
        ];
    }

    public function intro(): static
    {
        return $this->state(fn () => [
            'class_count' => 1,
            'price' => 99_000,
            'validity_days' => 14,
            'is_intro' => true,
        ]);
    }
}
