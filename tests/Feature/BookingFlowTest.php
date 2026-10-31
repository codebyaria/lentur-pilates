<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Resources\Site\SiteUrls;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\ClassType;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    private Package $intro;

    private Package $dropIn;

    protected function setUp(): void
    {
        parent::setUp();

        $this->intro = Package::factory()->intro()->create(['slug' => 'kelas-pertama', 'name' => 'Kelas Pertama', 'name_en' => 'First Class']);
        $this->dropIn = Package::factory()->create(['slug' => 'sekali-datang', 'class_count' => 1, 'price' => 175_000, 'sort' => 1]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(ClassSession $session, array $overrides = []): array
    {
        return [
            'class_session_id' => $session->id,
            'package' => 'kelas-pertama',
            'nickname' => 'Wiwid',
            'whatsapp' => '+62 812-9999-0001',
            'is_new_to_pilates' => true,
            'injury_notes' => 'Bahu kanan agak kaku.',
            ...$overrides,
        ];
    }

    public function test_booking_page_lists_schedule_and_packages_with_intro_first()
    {
        $session = ClassSession::factory()->create();

        $this->get('/booking')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('booking/flow')
            ->where('schedule.0.id', $session->id)
            ->where('packages.0.slug', 'kelas-pertama')
            ->where('existingPackageChoice', 'existing')
            ->where('initial.sessionId', null)
            ->where('urls.submit', route('booking.store'))
            ->where('urls.alternate', route('booking.en')));
    }

    public function test_links_from_the_landing_preselect_class_and_package_but_never_a_full_class()
    {
        $open = ClassSession::factory()->create();
        $full = ClassSession::factory()->for(ClassType::factory()->capacity(1))->create();
        Booking::factory()->for($full)->create();

        $this->get("/booking?jadwal={$open->id}&paket=sekali-datang")->assertInertia(fn (Assert $page) => $page
            ->where('initial.sessionId', $open->id)
            ->where('initial.package', 'sekali-datang'));

        $this->get("/booking?jadwal={$full->id}&paket=tidak-ada")->assertInertia(fn (Assert $page) => $page
            ->where('initial.sessionId', null)
            ->where('initial.package', null));
    }

    public function test_first_timer_books_with_the_intro_pack_and_sees_the_confirmation()
    {
        $session = ClassSession::factory()->startingAt(now()->next('Thursday')->setTime(19, 45))->create();

        $response = $this->post('/booking', $this->payload($session));

        $booking = Booking::query()->sole();
        $response->assertRedirect(SiteUrls::confirmation($booking));

        $member = $booking->member;
        $this->assertSame('081299990001', $member->whatsapp);
        $this->assertSame('0812-9999-0001', $member->formattedWhatsapp());
        $this->assertTrue($member->is_new_to_pilates);
        $this->assertSame('Bahu kanan agak kaku.', $member->injury_notes);
        $this->assertTrue($booking->is_first_class);
        $this->assertSame(BookingStatus::Confirmed, $booking->status);
        $this->assertTrue($booking->membership?->package->is($this->intro));
        $this->assertSame(PaymentStatus::Paid, $booking->membership->payment_status);
        $this->assertSame(0, $booking->membership->classes_remaining);

        $this->get(SiteUrls::confirmation($booking))->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('booking/confirmed')
            ->where('booking.code', $booking->code)
            ->where('booking.dayLabel', 'Kamis')
            ->where('booking.time', '19:45')
            ->where('booking.isFirstClass', true)
            ->where('booking.nickname', 'Wiwid')
            ->where('urls.shareClass', route('booking', ['jadwal' => $session->id])));
    }

    public function test_returning_member_can_use_an_active_pack_and_keeps_old_notes()
    {
        $member = Member::factory()->withInjuryNotes('Lutut kiri rewel.')->create(['whatsapp' => '0812-1111-2222']);
        $membership = Membership::factory()->for($member)->create(['classes_remaining' => 4]);
        Booking::factory()->for($member)->create(['status' => BookingStatus::Attended]);
        $session = ClassSession::factory()->create();

        $this->post('/booking', $this->payload($session, [
            'package' => 'existing',
            'whatsapp' => '6281211112222',
            'nickname' => 'Wulan',
            'is_new_to_pilates' => false,
            'injury_notes' => '',
        ]))->assertRedirect();

        $this->assertSame(1, Member::query()->count());
        $member->refresh();
        $this->assertSame('Wulan', $member->nickname);
        $this->assertSame('Lutut kiri rewel.', $member->injury_notes);
        $this->assertSame(3, $membership->refresh()->classes_remaining);
        $this->assertFalse($session->bookings()->sole()->is_first_class);
    }

    public function test_using_an_active_pack_without_one_is_refused_on_the_package_step()
    {
        $session = ClassSession::factory()->create();

        $this->from('/booking')
            ->post('/booking', $this->payload($session, ['package' => 'existing']))
            ->assertRedirect('/booking')
            ->assertSessionHasErrors(['package' => 'Belum ada paket aktif dengan sisa kelas.']);

        $this->assertSame(0, Booking::query()->count());
    }

    public function test_intro_pack_is_once_per_person()
    {
        $member = Member::factory()->create(['whatsapp' => '081299990001']);
        Membership::factory()->for($member)->forPackage($this->intro)->usedUp()->create();

        $this->from('/booking')
            ->post('/booking', $this->payload(ClassSession::factory()->create()))
            ->assertSessionHasErrors(['package' => 'Kelas Pertama hanya untuk kunjungan pertama. Pilih paket lain, ya.']);

        $this->assertSame(1, Membership::query()->count());
    }

    public function test_full_class_rolls_everything_back()
    {
        $session = ClassSession::factory()->for(ClassType::factory()->capacity(1))->create();
        Booking::factory()->for($session)->create();
        $membersBefore = Member::query()->count();

        $this->from('/booking')
            ->post('/booking', $this->payload($session, ['package' => 'sekali-datang']))
            ->assertSessionHasErrors(['class_session_id' => 'Kelas ini sudah ramai. Jam berikutnya masih lega.']);

        // Tidak ada member, paket, atau pembayaran simulasi yang tertinggal.
        $this->assertSame($membersBefore, Member::query()->count());
        $this->assertSame(0, Membership::query()->count());
    }

    public function test_cannot_book_the_same_class_twice()
    {
        $session = ClassSession::factory()->create();
        $this->post('/booking', $this->payload($session, ['package' => 'sekali-datang']));

        $this->from('/booking')
            ->post('/booking', $this->payload($session, ['package' => 'sekali-datang']))
            ->assertSessionHasErrors(['class_session_id' => 'Sudah terdaftar di kelas ini.']);

        $this->assertSame(1, Booking::query()->count());
    }

    public function test_validation_speaks_like_a_friend()
    {
        $this->from('/booking')
            ->post('/booking', ['whatsapp' => '12345', 'injury_notes' => str_repeat('a', 501)])
            ->assertSessionHasErrors([
                'class_session_id' => 'Pilih jadwal dulu, ya.',
                'package' => 'Pilih paket dulu, ya.',
                'nickname' => 'Boleh tahu nama panggilanmu?',
                'whatsapp' => 'Nomor WhatsApp-nya kurang pas. Contoh: 0812 3456 7890.',
                'is_new_to_pilates' => 'Pertama kali pilates atau sudah pernah?',
                'injury_notes' => 'Catatannya maksimal 500 karakter.',
            ]);
    }

    public function test_english_booking_uses_english_messages_and_urls()
    {
        $member = Member::factory()->create(['whatsapp' => '081299990001']);
        Booking::factory()->for($member)->create(['status' => BookingStatus::Attended]);
        $session = ClassSession::factory()->create();

        $this->from('/en/booking')
            ->post('/en/booking', $this->payload($session))
            ->assertSessionHasErrors(['package' => 'The First Class pack is for first visits only. Please pick another pack.']);

        $this->post('/en/booking', $this->payload($session, ['package' => 'sekali-datang']))
            ->assertRedirectContains('/en/booking/done/');
    }

    public function test_confirmation_and_calendar_need_a_valid_signature()
    {
        $session = ClassSession::factory()->startingAt(now()->addDays(2)->setTime(18, 30))->create();
        $this->post('/booking', $this->payload($session));
        $booking = Booking::query()->sole();

        $this->get("/booking/selesai/{$booking->code}")->assertForbidden();
        $this->get("/booking/{$booking->code}/kalender.ics")->assertForbidden();

        $ics = $this->get(SiteUrls::calendar($booking))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/calendar; charset=utf-8');

        // 18:30 WIB = 11:30 UTC.
        $this->assertStringContainsString('DTSTART:'.$session->starts_at->format('Ymd').'T113000Z', $ics->getContent());
        $this->assertStringContainsString('UID:'.$booking->code.'@lentur.test', $ics->getContent());
    }
}
