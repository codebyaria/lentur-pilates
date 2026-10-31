<?php

namespace App\Http\Controllers;

use App\Actions\CancelBooking;
use App\Enums\BookingStatus;
use App\Exceptions\BookingNotPossible;
use App\Http\Resources\Site\SiteUrls;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Peserta membatalkan booking-nya sendiri lewat tautan bertanda tangan dari halaman konfirmasi.
 * Sebelum batas batal gratis: kelas kembali ke paket. Sesudahnya: tempat dilepas, kelas tetap terhitung.
 */
class BookingCancellationController extends Controller
{
    public function show(Request $request, Booking $booking): Response
    {
        $booking->load(['classSession.classType', 'classSession.instructor', 'member', 'membership.package']);
        $session = $booking->classSession;

        return Inertia::render('booking/cancel', [
            'booking' => [
                'code' => $booking->code,
                'nickname' => $booking->member->nickname,
                'className' => $session->classType->localized('name'),
                'dateLabel' => $session->starts_at->isoFormat('dddd, D MMMM'),
                'time' => $session->starts_at->format('H:i'),
                'endTime' => $session->ends_at->format('H:i'),
                'instructor' => $session->instructor->name,
                'status' => $booking->status->value,
                'canCancel' => $booking->status === BookingStatus::Confirmed && $session->starts_at->isFuture(),
                'isFree' => $booking->canCancelForFree(),
                'deadline' => $booking->freeCancellationDeadline()->isoFormat('dddd, D MMM · HH:mm'),
                'packageName' => $booking->membership?->package->localized('name'),
                'classesRemaining' => $booking->membership?->classes_remaining,
            ],
            'justCancelled' => $request->session()->get('booking.cancelled'),
            'cancellationHours' => (int) config('lentur.free_cancellation_hours'),
            'urls' => [
                'submit' => SiteUrls::cancel($booking),
                'confirmation' => SiteUrls::confirmation($booking),
                'home' => SiteUrls::home(),
                'bookAnother' => SiteUrls::booking(),
            ],
        ]);
    }

    public function store(Booking $booking, CancelBooking $cancelBooking): RedirectResponse
    {
        $booking->load('classSession');

        if ($booking->classSession->starts_at->isPast()) {
            return back()->withErrors(['booking' => BookingNotPossible::notCancellable()->getMessage()]);
        }

        $refunded = $booking->canCancelForFree();

        try {
            $cancelBooking->handle($booking, $refunded);
        } catch (BookingNotPossible $exception) {
            return back()->withErrors(['booking' => $exception->getMessage()]);
        }

        return redirect()->to(SiteUrls::cancel($booking))
            ->with('booking.cancelled', ['refunded' => $refunded]);
    }
}
