<?php

namespace Database\Factories;

use App\Enums\ClassKind;
use App\Enums\ClassLevel;
use App\Models\ClassType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ClassType>
 */
class ClassTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Mat '.fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'kind' => ClassKind::Mat,
            'level' => ClassLevel::BaruKenal,
            'summary' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'duration_minutes' => 50,
            'capacity' => 8,
            'is_intro' => false,
            'color' => 'lime',
            'sort' => 0,
        ];
    }

    public function level(ClassLevel $level): static
    {
        return $this->state(fn () => ['level' => $level]);
    }

    public function capacity(int $capacity): static
    {
        return $this->state(fn () => ['capacity' => $capacity]);
    }
}
