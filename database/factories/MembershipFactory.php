<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Membership>
 */
class MembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'package_id' => Package::factory(),
            'classes_total' => 5,
            'classes_remaining' => 5,
            'price_paid' => 800_000,
            'payment_status' => PaymentStatus::Paid,
            'payment_reference' => 'SIM-'.Str::upper(Str::random(10)),
            'purchased_at' => now()->subDays(3),
            'expires_at' => now()->addDays(57),
        ];
    }

    public function forPackage(Package $package): static
    {
        return $this->state(fn () => [
            'package_id' => $package->id,
            'classes_total' => $package->class_count,
            'classes_remaining' => $package->class_count,
            'price_paid' => $package->price,
            'expires_at' => now()->addDays($package->validity_days),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => ['expires_at' => now()->subDay()]);
    }

    public function usedUp(): static
    {
        return $this->state(fn () => ['classes_remaining' => 0]);
    }
}
