<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\CarbonImmutable;
use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Peserta yang booking. Tidak butuh akun: cukup nama panggilan + WhatsApp.
 *
 * @property int $id
 * @property string $nickname
 * @property string $whatsapp
 * @property string|null $email
 * @property bool $is_new_to_pilates
 * @property string|null $injury_notes
 * @property int|null $referred_by_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['nickname', 'whatsapp', 'email', 'is_new_to_pilates', 'injury_notes', 'referred_by_id'])]
class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory;

    /**
     * @return HasMany<Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return BelongsTo<Member, $this>
     */
    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'referred_by_id');
    }

    /**
     * @return HasMany<Member, $this>
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Member::class, 'referred_by_id');
    }

    public function hasInjuryNotes(): bool
    {
        return filled($this->injury_notes);
    }

    /**
     * Belum pernah ikut kelas di Lentur (booking aktif mana pun dihitung).
     */
    public function isFirstTimer(): bool
    {
        return ! $this->bookings()->whereIn('status', BookingStatus::holdingSpot())->exists();
    }

    /**
     * Paket lunas yang masih punya sisa kelas dan belum kedaluwarsa;
     * yang paling cepat habis masa berlakunya dipakai lebih dulu.
     */
    public function usableMembership(): ?Membership
    {
        return $this->memberships()
            ->where('payment_status', PaymentStatus::Paid)
            ->where('classes_remaining', '>', 0)
            ->where('expires_at', '>', now())
            ->orderBy('expires_at')
            ->first();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_new_to_pilates' => 'boolean',
        ];
    }
}
