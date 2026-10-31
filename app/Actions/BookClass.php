<?php

namespace App\Actions;

use App\Enums\BookingStatus;
use App\Exceptions\BookingNotPossible;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\Member;
use App\Models\Membership;
use Illuminate\Support\Facades\DB;

/**
 * Mendaftarkan member ke satu jadwal dan memakai satu kelas dari paketnya.
 */
class BookClass
{
    /**
     * @throws BookingNotPossible
     */
    public function handle(ClassSession $session, Member $member, ?Membership $membership = null): Booking
    {
        return DB::transaction(function () use ($session, $member, $membership) {
            $session = ClassSession::query()->with('classType')->lockForUpdate()->findOrFail($session->id);

            if ($session->isCancelled() || $session->starts_at->isPast()) {
                throw BookingNotPossible::sessionNotBookable();
            }

            if ($session->isFull()) {
                throw BookingNotPossible::sessionFull();
            }

            $existing = Booking::query()
                ->where('class_session_id', $session->id)
                ->where('member_id', $member->id)
                ->first();

            if ($existing !== null && $existing->status !== BookingStatus::Cancelled) {
                throw BookingNotPossible::alreadyBooked();
            }

            $membership ??= $member->usableMembership();

            if ($membership === null || $membership->member_id !== $member->id || ! $membership->isUsable()) {
                throw BookingNotPossible::noUsablePackage();
            }

            $isFirstClass = $member->isFirstTimer();
            $membership->decrement('classes_remaining');

            // Pernah batal di jadwal yang sama: pakai lagi barisnya (unik per member per jadwal).
            $booking = $existing ?? new Booking([
                'class_session_id' => $session->id,
                'member_id' => $member->id,
            ]);

            $booking->fill([
                'membership_id' => $membership->id,
                'status' => BookingStatus::Confirmed,
                'is_first_class' => $isFirstClass,
                'cancelled_at' => null,
            ])->save();

            return $booking;
        });
    }
}
