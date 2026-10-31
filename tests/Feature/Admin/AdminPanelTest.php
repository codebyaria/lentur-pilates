<?php

namespace Tests\Feature\Admin;

use App\Enums\BookingStatus;
use App\Filament\Pages\WeeklySchedule;
use App\Filament\Resources\ClassSessions\Pages\ViewClassSession;
use App\Filament\Resources\ClassSessions\RelationManagers\BookingsRelationManager;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\ClassType;
use App\Models\Instructor;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Package;
use App\Models\User;
use Carbon\CarbonInterface;
use Database\Seeders\DatabaseSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::query()->where('is_admin', true)->firstOrFail());
    }

    public function test_every_admin_page_renders()
    {
        $session = ClassSession::query()->firstOrFail();
        $member = Member::query()->whereNotNull('injury_notes')->firstOrFail();

        $urls = [
            '/admin',
            '/admin/jadwal-mingguan',
            '/admin/jadwal-mingguan?minggu='.now()->addWeek()->toDateString(),
            '/admin/jadwal-mingguan?minggu=bukan-tanggal',
            '/admin/class-sessions',
            '/admin/class-sessions/create',
            "/admin/class-sessions/{$session->id}",
            "/admin/class-sessions/{$session->id}/edit",
            '/admin/bookings',
            '/admin/members',
            '/admin/members/create',
            "/admin/members/{$member->id}",
            "/admin/members/{$member->id}/edit",
            '/admin/packages',
            '/admin/packages/create',
            '/admin/packages/'.Package::query()->value('id').'/edit',
            '/admin/instructors',
            '/admin/instructors/'.Instructor::query()->value('id').'/edit',
            '/admin/class-types',
            '/admin/class-types/'.ClassType::query()->value('id').'/edit',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_weekly_schedule_shows_this_weeks_classes_and_navigates()
    {
        $thisWeek = ClassSession::query()->with('classType')->inWeekOf(now())->firstOrFail();

        Livewire::test(WeeklySchedule::class)
            ->assertSee($thisWeek->classType->name)
            ->assertSee('Keterisian')
            ->callAction('nextWeek')
            ->assertSet('week', now()->startOfWeek(CarbonInterface::MONDAY)->addWeek()->toDateString())
            ->callAction('previousWeek')
            ->callAction('previousWeek')
            ->assertSee('Studio istirahat.');
    }

    public function test_session_page_flags_first_timers_and_injury_notes()
    {
        $session = ClassSession::factory()->create();
        $newbie = Member::factory()->newToPilates()->withInjuryNotes('Bahu kanan kaku.')->create(['nickname' => 'Wiwid']);
        Booking::factory()->for($session)->for($newbie)->create(['is_first_class' => true]);

        $this->get("/admin/class-sessions/{$session->id}")->assertOk();

        Livewire::test(BookingsRelationManager::class, ['ownerRecord' => $session, 'pageClass' => ViewClassSession::class])
            ->assertSee('Wiwid')
            ->assertSee('Pertama kali')
            ->assertSee('Ada catatan')
            ->assertSee('Bahu kanan kaku.');
    }

    public function test_admin_can_add_a_participant_and_cancel_with_refund()
    {
        $session = ClassSession::factory()->startingAt(now()->addDays(3))->create();
        $membership = Membership::factory()->create(['classes_remaining' => 4]);

        $component = Livewire::test(BookingsRelationManager::class, ['ownerRecord' => $session, 'pageClass' => ViewClassSession::class])
            ->callAction(TestAction::make('addParticipant')->table(), data: ['member_id' => $membership->member_id])
            ->assertHasNoFormErrors()
            ->assertNotified();

        $booking = $session->bookings()->sole();
        $this->assertSame(3, $membership->refresh()->classes_remaining);

        $component
            ->assertActionHidden(TestAction::make('attended')->table($booking))
            ->callAction([TestAction::make('cancel')->table($booking)], data: ['refund' => true]);

        $this->assertSame(BookingStatus::Cancelled, $booking->refresh()->status);
        $this->assertSame(4, $membership->refresh()->classes_remaining);
    }

    public function test_adding_to_a_full_class_is_disabled()
    {
        $session = ClassSession::factory()->for(ClassType::factory()->capacity(1))->create();
        Booking::factory()->for($session)->create();

        Livewire::test(BookingsRelationManager::class, ['ownerRecord' => $session, 'pageClass' => ViewClassSession::class])
            ->assertActionDisabled(TestAction::make('addParticipant')->table());
    }

    public function test_cancelling_a_class_from_its_page_refunds_participants()
    {
        $session = ClassSession::factory()->startingAt(now()->addDays(2))->create();
        $membership = Membership::factory()->create(['classes_remaining' => 1]);
        Booking::factory()->for($session)->for($membership->member)->for($membership)->create();

        Livewire::test(ViewClassSession::class, ['record' => $session->getRouteKey()])
            ->callAction('cancelSession')
            ->assertNotified('Kelas dibatalkan');

        $this->assertTrue($session->refresh()->isCancelled());
        $this->assertSame(2, $membership->refresh()->classes_remaining);
    }
}
