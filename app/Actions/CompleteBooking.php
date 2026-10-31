<?php

namespace App\Actions;

use App\Exceptions\BookingNotPossible;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\Member;
use App\Models\Package;
use Illuminate\Support\Facades\DB;

/**
 * Alur booking publik dalam satu transaksi: kenalan (member baru atau lama),
 * beli paket (simulasi) atau pakai paket aktif, lalu daftar ke kelas.
 * Gagal di langkah mana pun = tidak ada yang tersimpan.
 */
class CompleteBooking
{
    public const USE_EXISTING_PACKAGE = 'existing';

    public function __construct(
        private PurchasePackage $purchasePackage,
        private BookClass $bookClass,
    ) {}

    /**
     * @param  array{nickname: string, whatsapp: string, is_new_to_pilates: bool, injury_notes?: string|null}  $member
     *
     * @throws BookingNotPossible
     */
    public function handle(ClassSession $session, string $packageChoice, array $member): Booking
    {
        return DB::transaction(function () use ($session, $packageChoice, $member) {
            $person = Member::findByWhatsapp($member['whatsapp']) ?? new Member(['whatsapp' => $member['whatsapp']]);

            $person->fill([
                'nickname' => $member['nickname'],
                'is_new_to_pilates' => $member['is_new_to_pilates'],
            ]);

            // Catatan lama tidak dihapus kalau kolomnya dibiarkan kosong.
            if (filled($member['injury_notes'] ?? null)) {
                $person->injury_notes = $member['injury_notes'];
            }

            if ($packageChoice === self::USE_EXISTING_PACKAGE) {
                $person->save();
                $membership = $person->usableMembership() ?? throw BookingNotPossible::noUsablePackage();
            } else {
                $package = Package::query()->where('slug', $packageChoice)->where('is_active', true)->first()
                    ?? throw BookingNotPossible::packageUnavailable();

                if ($package->is_intro && ! $person->canUseIntroOffer()) {
                    throw BookingNotPossible::introAlreadyUsed();
                }

                $person->save();
                $membership = $this->purchasePackage->handle($person, $package);
            }

            return $this->bookClass->handle($session, $person, $membership);
        });
    }
}
