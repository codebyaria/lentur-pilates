<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Package;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_demo_has_an_admin_who_can_open_the_panel()
    {
        $admin = User::query()->where('is_admin', true)->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_regular_users_cannot_open_the_panel()
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_every_upcoming_session_has_people_and_full_ones_have_an_alternative()
    {
        $upcoming = ClassSession::query()->with('classType')->withSpotCounts()->bookable()->get();
        $this->assertNotEmpty($upcoming);

        foreach ($upcoming as $session) {
            $this->assertGreaterThan(0, $session->bookedCount(), "Kosong: sesi #{$session->id}");
            $this->assertLessThanOrEqual($session->effectiveCapacity(), $session->bookedCount());
        }

        $full = $upcoming->filter->isFull();
        $this->assertNotEmpty($full, 'Demo butuh minimal satu kelas penuh.');

        foreach ($full as $session) {
            $this->assertNotNull($session->nextAvailableAlternative());
        }
    }

    public function test_demo_data_is_consistent()
    {
        $this->assertSame(0, Membership::query()->where('classes_remaining', '<', 0)->count());
        $this->assertTrue(Package::query()->available()->firstOrFail()->is_intro);
        $this->assertGreaterThan(0, Member::query()->whereNotNull('injury_notes')->count());
        $this->assertGreaterThan(0, Booking::query()->where('is_first_class', true)->count());

        // Kelas pertama hanya untuk yang memang baru pertama kali.
        Booking::query()->where('is_first_class', true)->with('member')->get()
            ->each(fn (Booking $booking) => $this->assertTrue($booking->member->is_new_to_pilates));
    }
}
