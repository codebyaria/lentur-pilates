<?php

namespace Tests\Feature\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberAndBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_usable_membership_skips_expired_used_up_and_unpaid_packages()
    {
        $member = Member::factory()->create();

        Membership::factory()->for($member)->expired()->create();
        Membership::factory()->for($member)->usedUp()->create();
        Membership::factory()->for($member)->create(['payment_status' => PaymentStatus::Pending]);
        $later = Membership::factory()->for($member)->create(['expires_at' => now()->addDays(60)]);
        $sooner = Membership::factory()->for($member)->create(['expires_at' => now()->addDays(10)]);

        $this->assertTrue($member->usableMembership()?->is($sooner));
        $this->assertTrue($later->isUsable());
    }

    public function test_member_without_usable_package_gets_null()
    {
        $member = Member::factory()->create();
        Membership::factory()->for($member)->usedUp()->create();

        $this->assertNull($member->usableMembership());
    }

    public function test_first_timer_and_injury_flags()
    {
        $newbie = Member::factory()->newToPilates()->withInjuryNotes()->create();
        $regular = Member::factory()->create();
        Booking::factory()->for($regular)->create(['status' => BookingStatus::Attended]);

        $this->assertTrue($newbie->isFirstTimer());
        $this->assertTrue($newbie->hasInjuryNotes());
        $this->assertFalse($regular->isFirstTimer());
        $this->assertFalse($regular->hasInjuryNotes());
    }

    public function test_cancelled_bookings_do_not_count_toward_history()
    {
        $member = Member::factory()->create();
        Booking::factory()->cancelled()->for($member)->create();

        $this->assertTrue($member->isFirstTimer());
    }

    public function test_bookings_get_a_readable_unique_code()
    {
        $bookings = Booking::factory()->count(3)->create();

        foreach ($bookings as $booking) {
            $this->assertMatchesRegularExpression('/^LTR-[A-Z0-9]{6}$/', $booking->code);
        }

        $this->assertCount(3, $bookings->pluck('code')->unique());
    }

    public function test_free_cancellation_closes_before_class_starts()
    {
        config(['lentur.free_cancellation_hours' => 12]);
        $session = ClassSession::factory()->startingAt(now()->addDay()->setTime(18, 0))->create();
        $booking = Booking::factory()->for($session)->create();

        $this->travelTo($session->starts_at->subHours(13));
        $this->assertTrue($booking->canCancelForFree());

        $this->travelTo($session->starts_at->subHours(11));
        $this->assertFalse($booking->canCancelForFree());
    }

    public function test_only_confirmed_bookings_can_be_cancelled()
    {
        $booking = Booking::factory()->cancelled()->create();

        $this->assertFalse($booking->canCancelForFree());
    }

    public function test_intro_package_is_listed_first_and_prices_are_formatted()
    {
        Package::factory()->create(['sort' => 0]);
        $intro = Package::factory()->intro()->create(['sort' => 5]);
        Package::factory()->create(['is_active' => false]);

        $packages = Package::query()->available()->get();

        $this->assertTrue($packages->first()?->is($intro));
        $this->assertCount(2, $packages);
        $this->assertSame('Rp 99.000', Package::formatRupiah($intro->price));
    }
}
