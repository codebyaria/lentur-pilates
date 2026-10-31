<?php

namespace Tests\Feature\Models;

use App\Enums\BookingStatus;
use App\Enums\ClassLevel;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\ClassType;
use App\Models\Instructor;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_capacity_follows_class_type_unless_overridden()
    {
        $type = ClassType::factory()->capacity(6)->create();

        $default = ClassSession::factory()->for($type)->create();
        $override = ClassSession::factory()->for($type)->create(['capacity' => 4]);

        $this->assertSame(6, $default->effectiveCapacity());
        $this->assertSame(4, $override->effectiveCapacity());
    }

    public function test_cancelled_bookings_free_up_a_spot()
    {
        $session = ClassSession::factory()
            ->for(ClassType::factory()->capacity(2))
            ->create();

        Booking::factory()->for($session)->create();
        Booking::factory()->for($session)->create(['status' => BookingStatus::Attended]);
        $this->assertTrue($session->isFull());

        $session->bookings()->first()?->update(['status' => BookingStatus::Cancelled]);

        $this->assertSame(1, $session->spotsLeft());
        $this->assertFalse($session->isFull());
    }

    public function test_spot_counts_can_be_eager_loaded()
    {
        $session = ClassSession::factory()->create();
        Booking::factory()->count(2)->for($session)->create();
        Booking::factory()->cancelled()->for($session)->create();

        $loaded = ClassSession::query()->withSpotCounts()->findOrFail($session->id);

        $this->assertSame(2, $loaded->active_bookings_count);
        $this->assertSame(2, $loaded->bookedCount());
    }

    public function test_full_session_suggests_the_next_open_slot_of_the_same_class_first()
    {
        $instructor = Instructor::factory()->create();
        $reformer = ClassType::factory()->level(ClassLevel::MulaiAkrab)->capacity(1)->create();
        $mat = ClassType::factory()->level(ClassLevel::MulaiAkrab)->capacity(5)->create();
        $start = now()->addDays(2)->setTime(18, 30);

        $full = $this->makeSession($reformer, $instructor, $start);
        Booking::factory()->for($full)->create();

        $anotherFull = $this->makeSession($reformer, $instructor, $start->addHour());
        Booking::factory()->for($anotherFull)->create();

        // Jenis lain, lebih cepat, tapi kalah prioritas dari jenis yang sama.
        $this->makeSession($mat, $instructor, $start->addMinutes(30));
        $sameClassLater = $this->makeSession($reformer, $instructor, $start->addDay());

        $this->assertTrue($full->nextAvailableAlternative()?->is($sameClassLater));
    }

    public function test_alternative_falls_back_to_another_class_of_the_same_level()
    {
        $instructor = Instructor::factory()->create();
        $reformer = ClassType::factory()->level(ClassLevel::BaruKenal)->capacity(1)->create();
        $mat = ClassType::factory()->level(ClassLevel::BaruKenal)->create();
        $harder = ClassType::factory()->level(ClassLevel::SudahLengket)->create();
        $start = now()->addDays(2)->setTime(9, 0);

        $full = $this->makeSession($reformer, $instructor, $start);
        Booking::factory()->for($full)->create();
        $this->makeSession($harder, $instructor, $start->addHour());
        $mat = $this->makeSession($mat, $instructor, $start->addHours(2));

        $this->assertTrue($full->nextAvailableAlternative()?->is($mat));
    }

    public function test_no_alternative_when_nothing_else_is_open()
    {
        $session = ClassSession::factory()->create();
        ClassSession::factory()->cancelled()->for($session->classType)->create();

        $this->assertNull($session->nextAvailableAlternative());
    }

    public function test_in_week_of_scope_returns_monday_to_sunday()
    {
        $this->travelTo(now()->startOfWeek(CarbonInterface::MONDAY)->addDays(2)->setTime(10, 0));
        $monday = now()->startOfWeek(CarbonInterface::MONDAY);

        $inside = [
            ClassSession::factory()->startingAt($monday->setTime(7, 0))->create(),
            ClassSession::factory()->startingAt($monday->addDays(6)->setTime(20, 0))->create(),
        ];
        ClassSession::factory()->startingAt($monday->subMinute())->create();
        ClassSession::factory()->startingAt($monday->addWeek())->create();

        $this->assertEqualsCanonicalizing(
            collect($inside)->pluck('id')->all(),
            ClassSession::query()->inWeekOf()->pluck('id')->all(),
        );
    }

    private function makeSession(ClassType $type, Instructor $instructor, CarbonInterface $startsAt): ClassSession
    {
        return ClassSession::factory()
            ->for($type)
            ->for($instructor)
            ->startingAt($startsAt)
            ->create();
    }
}
