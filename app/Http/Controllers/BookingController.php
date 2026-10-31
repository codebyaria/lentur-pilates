<?php

namespace App\Http\Controllers;

use App\Actions\CompleteBooking;
use App\Exceptions\BookingNotPossible;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\Site\PackageResource;
use App\Http\Resources\Site\SiteUrls;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\Package;
use App\Support\BookingCalendar;
use App\Support\SiteSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Booking 4 langkah: jadwal -> kelas & paket -> kenalan -> konfirmasi.
 * Langkah 1–3 di satu halaman (state di browser); konfirmasi = halaman sendiri setelah tersimpan.
 */
class BookingController extends Controller
{
    public function create(Request $request): Response
    {
        $packages = Package::query()->available()->get();
        $schedule = SiteSchedule::props($request);

        // Kelas penuh tidak dipilihkan: pengunjung mulai di langkah 1 dan melihat saran jam lain.
        $selectedSession = collect($schedule['schedule'])
            ->first(fn (array $session) => $session['id'] === $request->integer('jadwal') && ! $session['isFull']);
        $selectedPackage = $packages->firstWhere('slug', $request->string('paket')->toString());
        $otherLocale = app()->getLocale() === 'en' ? 'id' : 'en';

        return Inertia::render('booking/flow', [
            ...$schedule,
            'packages' => PackageResource::collection($packages)->resolve($request),
            'existingPackageChoice' => CompleteBooking::USE_EXISTING_PACKAGE,
            'cancellationHours' => (int) config('lentur.free_cancellation_hours'),
            'initial' => [
                'sessionId' => $selectedSession['id'] ?? null,
                'package' => $selectedPackage?->slug,
            ],
            'urls' => [
                'home' => SiteUrls::home(),
                'submit' => SiteUrls::bookingStore(),
                'alternate' => SiteUrls::booking(
                    $selectedSession['id'] ?? null,
                    $otherLocale,
                    array_filter(['paket' => $selectedPackage?->slug]),
                ),
            ],
        ]);
    }

    public function store(StoreBookingRequest $request, CompleteBooking $completeBooking): RedirectResponse
    {
        $session = ClassSession::query()->findOrFail($request->integer('class_session_id'));

        try {
            $booking = $completeBooking->handle($session, $request->string('package')->toString(), [
                'nickname' => $request->string('nickname')->trim()->toString(),
                'whatsapp' => $request->string('whatsapp')->toString(),
                'is_new_to_pilates' => $request->boolean('is_new_to_pilates'),
                'injury_notes' => $request->string('injury_notes')->trim()->toString() ?: null,
            ]);
        } catch (BookingNotPossible $exception) {
            return back()->withErrors([$exception->field => $exception->getMessage()]);
        }

        return redirect()->to(SiteUrls::confirmation($booking));
    }

    public function confirmed(Booking $booking): Response
    {
        $booking->load(['classSession.classType', 'classSession.instructor', 'member', 'membership.package']);
        $session = $booking->classSession;

        return Inertia::render('booking/confirmed', [
            'booking' => [
                'code' => $booking->code,
                'nickname' => $booking->member->nickname,
                'className' => $session->classType->localized('name'),
                'dayLabel' => $session->starts_at->isoFormat('dddd'),
                'dateLabel' => $session->starts_at->isoFormat('dddd, D MMMM'),
                'time' => $session->starts_at->format('H:i'),
                'endTime' => $session->ends_at->format('H:i'),
                'instructor' => $session->instructor->name,
                'room' => $session->room,
                'isFirstClass' => $booking->is_first_class,
                'packageName' => $booking->membership?->package->localized('name'),
                'classesRemaining' => $booking->membership?->classes_remaining,
                'cancelDeadline' => $booking->freeCancellationDeadline()->isoFormat('dddd, D MMM · HH:mm'),
            ],
            'studio' => [
                'name' => config('lentur.studio.name'),
                'address' => config('lentur.studio.address'),
            ],
            'urls' => [
                'home' => SiteUrls::home(),
                'calendar' => SiteUrls::calendar($booking),
                'googleCalendar' => BookingCalendar::googleUrl($booking),
                'shareClass' => SiteUrls::booking($session->id),
                'bookAnother' => SiteUrls::booking(),
            ],
        ]);
    }

    public function calendar(Booking $booking): HttpResponse
    {
        $booking->load(['classSession.classType', 'classSession.instructor']);

        return response(BookingCalendar::ics($booking), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="lentur-'.$booking->code.'.ics"',
        ]);
    }
}
