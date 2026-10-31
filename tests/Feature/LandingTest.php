<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\ClassType;
use App\Models\Instructor;
use App\Models\Package;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_shows_the_next_seven_days_in_indonesian()
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/');

        $response->assertOk()->assertSee('<html lang="id"', false);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('landing')
            ->where('locale', 'id')
            ->has('days', 7)
            ->where('days.0.isToday', true)
            ->where('packages.0.isIntro', true)
            ->where('packages.0.priceLabel', 'Rp 99.000')
            ->where('classTypes.0.name', 'Kenalan Dulu')
            ->where('cancellationHours', 12)
            ->has('instructors', 4)
            ->where('urls.alternateHome', route('home.en'))
            ->has('schedule.0', fn (Assert $session) => $session
                ->whereType('id', 'integer')
                ->where('bookingUrl', fn (string $url) => str_starts_with($url, route('booking')))
                ->etc()));

        // Hanya jadwal yang belum mulai, dalam 7 hari.
        $schedule = $response->viewData('page')['props']['schedule'];
        $limit = today()->addDays(7)->toDateString();

        foreach ($schedule as $session) {
            $this->assertTrue(ClassSession::query()->findOrFail($session['id'])->starts_at->isFuture());
            $this->assertLessThan($limit, $session['day']);
        }
    }

    public function test_full_classes_stay_visible_with_a_suggested_alternative()
    {
        $instructor = Instructor::factory()->create();
        $type = ClassType::factory()->capacity(1)->create();
        $full = ClassSession::factory()->for($type)->for($instructor)->startingAt(now()->addDay()->setTime(18, 30))->create();
        $next = ClassSession::factory()->for($type)->for($instructor)->startingAt(now()->addDay()->setTime(19, 45))->create();
        Booking::factory()->for($full)->create();

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('schedule.0.id', $full->id)
            ->where('schedule.0.isFull', true)
            ->where('schedule.0.alternative.id', $next->id)
            ->where('schedule.0.alternative.time', '19:45')
            ->where('schedule.1.isFull', false)
            ->where('schedule.1.spotsLeft', 1));
    }

    public function test_english_site_uses_english_copy_with_indonesian_fallback()
    {
        $this->seed(DatabaseSeeder::class);
        Package::factory()->create(['name' => 'Paket Uji', 'name_en' => null, 'sort' => 99]);

        $response = $this->get('/en');

        $response->assertOk()->assertSee('<html lang="en"', false);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('landing')
            ->where('locale', 'en')
            ->where('classTypes.0.name', 'Say Hello')
            ->where('levels.0.label', 'Just Met')
            ->where('packages.0.name', 'First Class')
            ->where('instructors.0.specialties', ['Mat', 'First class'])
            ->where('packages', fn ($packages) => collect($packages)->contains('name', 'Paket Uji'))
            ->where('urls.alternateHome', route('home'))
            ->where('schedule.0.bookingUrl', fn (string $url) => str_starts_with($url, route('booking.en'))));
    }

    public function test_inactive_instructors_and_packages_are_hidden()
    {
        Instructor::factory()->create(['name' => 'Tersembunyi', 'is_active' => false]);
        Package::factory()->create(['name' => 'Tidak dijual', 'is_active' => false]);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->has('instructors', 0)
            ->has('packages', 0));
    }
}
