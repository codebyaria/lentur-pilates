<?php

namespace App\Actions;

use App\Enums\BookingStatus;
use App\Models\ClassSession;
use Illuminate\Support\Facades\DB;

/**
 * Studio membatalkan jadwal: semua peserta terdaftar dibatalkan dan kuotanya kembali.
 */
class CancelClassSession
{
    public function __construct(private CancelBooking $cancelBooking) {}

    /**
     * @return int jumlah peserta yang ikut dibatalkan
     */
    public function handle(ClassSession $session): int
    {
        return DB::transaction(function () use ($session) {
            $session->update(['cancelled_at' => now()]);

            $bookings = $session->bookings()->with('membership')->where('status', BookingStatus::Confirmed)->get();

            foreach ($bookings as $booking) {
                $this->cancelBooking->handle($booking, refund: true);
            }

            return $bookings->count();
        });
    }
}
