<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nickname' => fake()->firstName(),
            // Nomor fiktif: blok 0812-0000-xxxx tidak dipakai di demo mana pun.
            'whatsapp' => '0812-0000-'.fake()->unique()->numerify('####'),
            'email' => null,
            'is_new_to_pilates' => false,
            'injury_notes' => null,
            'referred_by_id' => null,
        ];
    }

    public function newToPilates(): static
    {
        return $this->state(fn () => ['is_new_to_pilates' => true]);
    }

    public function withInjuryNotes(string $notes = 'Lutut kiri agak rewel kalau ditekuk dalam.'): static
    {
        return $this->state(fn () => ['injury_notes' => $notes]);
    }
}
