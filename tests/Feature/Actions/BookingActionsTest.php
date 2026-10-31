<?php

namespace Tests\Feature\Actions;

use App\Actions\BookClass;
use App\Actions\CancelBooking;
use App\Actions\CancelClassSession;
use App\Actions\PurchasePackage;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\BookingNotPossible;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\ClassType;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingActionsTest extends TestCase
{
    use RefreshDatabase;

    private BookClass $bookClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookClass = app(BookClass::class);
    }

    public function test_booking_uses_one_class_and_marks_the_first_class()
    {
        $member = Member::factory()->newToPilates()->create();
        $membership = Membership::factory()->for($member)->create(['classes_remaining' => 3]);
        $session = ClassSession::factory()->create();

        $booking = $this->bookClass->handle($session, $member);

        $this->assertSame(BookingStatus::Confirmed, $booking->status);
        $this->assertTrue($booking->is_first_class);
        $this->assertTrue($booking->membership?->is($membership));
        $this->assertSame(2, $membership->refresh()->classes_remaining);

        $second = $this->bookClass->handle(ClassSession::factory()->create(), $member);
        $this->assertFalse($second->is_first_class);
    }

    public function test_full_session_is_refused_without_using_quota()
    {
        $session = ClassSession::factory()->for(ClassType::factory()->capacity(1))->create();
        Booking::factory()->for($session)->create();
        $membership = Membership::factory()->create(['classes_remaining' => 2]);

        $this->expectExceptionObject(BookingNotPossible::sessionFull());

        try {
            $this->bookClass->handle($session, $membership->member);
        } finally {
            $this->assertSame(2, $membership->refresh()->classes_remaining);
        }
    }

    public function test_cannot_book_twice_past_sessions_or_without_package()
    {
        $membership = Membership::factory()->create();
        $member = $membership->member;
        $session = ClassSession::factory()->create();
        $this->bookClass->handle($session, $member);

        $this->assertThrows(fn () => $this->bookClass->handle($session, $member), BookingNotPossible::class, 'Sudah terdaftar di kelas ini.');

        $past = ClassSession::factory()->startingAt(now()->subHour())->create();
        $this->assertThrows(fn () => $this->bookClass->handle($past, $member), BookingNotPossible::class);

        $cancelled = ClassSession::factory()->cancelled()->create();
        $this->assertThrows(fn () => $this->bookClass->handle($cancelled, $member), BookingNotPossible::class);

        $broke = Member::factory()->create();
        Membership::factory()->for($broke)->create(['payment_status' => PaymentStatus::Pending]);
        $this->assertThrows(fn () => $this->bookClass->handle(ClassSession::factory()->create(), $broke), BookingNotPossible::class, 'Belum ada paket aktif dengan sisa kelas.');
    }

    public function test_someone_elses_package_cannot_be_used()
    {
        $member = Member::factory()->create();
        $otherPackage = Membership::factory()->create();

        $this->assertThrows(
            fn () => $this->bookClass->handle(ClassSession::factory()->create(), $member, $otherPackage),
            BookingNotPossible::class,
        );
    }

    public function test_rebooking_after_cancelling_reuses_the_booking()
    {
        $membership = Membership::factory()->create(['classes_remaining' => 2]);
        $session = ClassSession::factory()->startingAt(now()->addDays(3))->create();
        $booking = $this->bookClass->handle($session, $membership->member);

        app(CancelBooking::class)->handle($booking);
        $again = $this->bookClass->handle($session, $membership->member);

        $this->assertTrue($again->is($booking));
        $this->assertSame(BookingStatus::Confirmed, $again->status);
        $this->assertNull($again->cancelled_at);
        $this->assertSame(1, $membership->refresh()->classes_remaining);
    }

    public function test_cancelling_refunds_only_before_the_deadline_unless_told_otherwise()
    {
        config(['lentur.free_cancellation_hours' => 12]);
        $cancel = app(CancelBooking::class);
        $membership = Membership::factory()->create(['classes_remaining' => 5]);

        $early = $this->bookClass->handle(ClassSession::factory()->startingAt(now()->addDays(2))->create(), $membership->member);
        $late = $this->bookClass->handle(ClassSession::factory()->startingAt(now()->addHours(3))->create(), $membership->member);
        $lateButKind = $this->bookClass->handle(ClassSession::factory()->startingAt(now()->addHours(4))->create(), $membership->member);
        $this->assertSame(2, $membership->refresh()->classes_remaining);

        $cancel->handle($early);
        $cancel->handle($late);
        $cancel->handle($lateButKind, refund: true);

        $this->assertSame(4, $membership->refresh()->classes_remaining);
        $this->assertSame(BookingStatus::Cancelled, $late->refresh()->status);
        $this->assertThrows(fn () => $cancel->handle($late), BookingNotPossible::class);
    }

    public function test_cancelling_a_class_refunds_everyone_booked()
    {
        $session = ClassSession::factory()->create();
        $memberships = Membership::factory()->count(3)->create(['classes_remaining' => 1]);
        $memberships->each(fn (Membership $membership) => $this->bookClass->handle($session, $membership->member));

        $count = app(CancelClassSession::class)->handle($session);

        $this->assertSame(3, $count);
        $this->assertTrue($session->refresh()->isCancelled());
        $memberships->each(fn (Membership $membership) => $this->assertSame(1, $membership->refresh()->classes_remaining));
    }

    public function test_purchasing_a_package_is_simulated_as_paid()
    {
        $member = Member::factory()->create();
        $package = Package::factory()->create(['class_count' => 5, 'price' => 800_000, 'validity_days' => 60]);

        $membership = app(PurchasePackage::class)->handle($member, $package);

        $this->assertSame(PaymentStatus::Paid, $membership->payment_status);
        $this->assertSame(5, $membership->classes_remaining);
        $this->assertStringStartsWith('SIM-', $membership->payment_reference);
        $this->assertTrue($member->usableMembership()?->is($membership));
    }

    public function test_end_time_follows_the_class_duration()
    {
        $type = ClassType::factory()->create(['duration_minutes' => 55]);
        $session = ClassSession::factory()->for($type)->startingAt(now()->addDay()->setTime(18, 30))->create();

        $this->assertSame('19:25', $session->ends_at->format('H:i'));

        $session->update(['starts_at' => $session->starts_at->setTime(7, 0)]);
        $this->assertSame('07:55', $session->refresh()->ends_at->format('H:i'));
    }
}
