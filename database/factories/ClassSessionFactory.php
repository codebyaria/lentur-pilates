<?php

namespace Database\Factories;

use App\Models\ClassSession;
use App\Models\ClassType;
use App\Models\Instructor;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassSession>
 */
class ClassSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = now()->addDays(2)->setTime(9, 0);

        return [
            'class_type_id' => ClassType::factory(),
            'instructor_id' => Instructor::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMinutes(50),
            'capacity' => null,
            'room' => 'Studio Utama',
            'cancelled_at' => null,
        ];
    }

    public function startingAt(CarbonInterface $startsAt): static
    {
        return $this->state(fn () => [
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMinutes(50),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['cancelled_at' => now()]);
    }
}
