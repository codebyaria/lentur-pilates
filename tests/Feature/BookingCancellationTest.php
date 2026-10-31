<?php

namespace Tests\Feature;

use App\Actions\BookClass;
use App\Enums\BookingStatus;
use App\Http\Resources\Site\SiteUrls;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\ClassType;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BookingCancellationTest extends TestCase
{
    use RefreshDatabase;

    private function book(ClassSession $session, int $classesRemaining = 3): Booking
    {
        $membership = Membership::factory()->create(['classes_remaining' => $classesRemaining]);

        return app(BookClass::class)->handle($session, $membership->member);
    }

    public function test_cancel_page_needs_a_valid_signature()
    {
        $booking = $this->book(ClassSession::factory()->create());

        $this->get("/booking/{$booking->code}/batal")->assertForbidden();
        $this->post("/booking/{$booking->code}/batal")->assertForbidden();
        $this->get(SiteUrls::cancel($booking))->assertOk();
    }

    public function test_confirmation_links_to_the_cancel_page()
    {
        $booking = $this->book(ClassSession::factory()->create());

        $this->get(SiteUrls::confirmation($booking))->assertInertia(fn (Assert $page) => $page
            ->where('urls.cancel', SiteUrls::cancel($booking)));
    }

    public function test_cancelling_before_the_deadline_returns_the_class_and_frees_the_spot()
    {
        config(['lentur.free_cancellation_hours' => 12]);
        $session = ClassSession::factory()->for(ClassType::factory()->capacity(1))->startingAt(now()->addDays(2))->create();
        $booking = $this->book($session, 3);
        $this->assertTrue($session->isFull());

        $this->get(SiteUrls::cancel($booking))->assertInertia(fn (Assert $page) => $page
            ->component('booking/cancel')
            ->where('booking.canCancel', true)
            ->where('booking.isFree', true)
            ->where('justCancelled', null));

        $this->post(SiteUrls::cancel($booking))->assertRedirect(SiteUrls::cancel($booking));

        $booking->refresh();
        $this->assertSame(BookingStatus::Cancelled, $booking->status);
        $this->assertSame(3, $booking->membership?->classes_remaining);
        $this->assertFalse($session->refresh()->isFull());

        $this->get(SiteUrls::cancel($booking))->assertInertia(fn (Assert $page) => $page
            ->where('justCancelled.refunded', true)
            ->where('booking.status', 'cancelled')
            ->where('booking.canCancel', false));
    }

    public function test_late_cancellation_frees_the_spot_but_the_class_still_counts()
    {
        config(['lentur.free_cancellation_hours' => 12]);
        $booking = $this->book(ClassSession::factory()->startingAt(now()->addHours(3))->create(), 3);

        $this->get(SiteUrls::cancel($booking))->assertInertia(fn (Assert $page) => $page
            ->where('booking.isFree', false)
            ->where('booking.canCancel', true));

        $this->post(SiteUrls::cancel($booking));

        $this->assertSame(BookingStatus::Cancelled, $booking->refresh()->status);
        $this->assertSame(2, $booking->membership?->classes_remaining);
        $this->get(SiteUrls::cancel($booking))->assertInertia(fn (Assert $page) => $page
            ->where('justCancelled.refunded', false));
    }

    public function test_cancelling_twice_or_after_the_class_started_is_refused()
    {
        $booking = $this->book(ClassSession::factory()->startingAt(now()->addDays(2))->create());
        $this->post(SiteUrls::cancel($booking));

        $this->from(SiteUrls::cancel($booking))
            ->post(SiteUrls::cancel($booking))
            ->assertSessionHasErrors(['booking' => 'Booking ini tidak bisa dibatalkan lagi.']);

        $started = $this->book(ClassSession::factory()->startingAt(now()->addHour())->create());
        $this->travel(2)->hours();

        $this->get(SiteUrls::cancel($started))->assertInertia(fn (Assert $page) => $page
            ->where('booking.canCancel', false));
        $this->post(SiteUrls::cancel($started))->assertSessionHasErrors('booking');
        $this->assertSame(BookingStatus::Confirmed, $started->refresh()->status);
    }

    public function test_english_cancel_page_uses_english_urls()
    {
        $booking = $this->book(ClassSession::factory()->create());

        $this->get(SiteUrls::cancel($booking, 'en'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('locale', 'en')
                ->where('urls.submit', fn (string $url) => str_contains($url, "/en/booking/{$booking->code}/cancel")));
    }
}
