<?php

namespace App\Actions;

use App\Enums\BookingStatus;
use App\Exceptions\BookingNotPossible;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class CancelBooking
{
    /**
     * Kuota kembali ke paket bila dibatalkan sebelum batas batal gratis,
     * kecuali ditentukan lain (mis. admin memutuskan tetap mengembalikan).
     *
     * @throws BookingNotPossible
     */
    public function handle(Booking $booking, ?bool $refund = null): Booking
    {
        if ($booking->status !== BookingStatus::Confirmed) {
            throw BookingNotPossible::notCancellable();
        }

        $refund ??= $booking->canCancelForFree();

        return DB::transaction(function () use ($booking, $refund) {
            $booking->update([
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now(),
            ]);

            if ($refund) {
                $booking->membership?->increment('classes_remaining');
            }

            return $booking;
        });
    }
}
