<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\CarbonImmutable;
use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
     * Paket yang sedang dipakai (sama dengan usableMembership), bisa di-eager-load.
     *
     * @return HasOne<Membership, $this>
     */
    public function currentMembership(): HasOne
    {
        return $this->hasOne(Membership::class)->ofMany(
            ['expires_at' => 'min', 'id' => 'min'],
            fn ($query) => $query
                ->where('payment_status', PaymentStatus::Paid)
                ->where('classes_remaining', '>', 0)
                ->where('expires_at', '>', now()),
        );
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

    /**
     * WhatsApp disimpan sebagai angka saja, format lokal: 0812...
     * "+62 812-3456", "62812..." dan "0812-3456" jadi sama.
     */
    public static function normalizeWhatsapp(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '0'.$digits;
        }

        return $digits;
    }

    /**
     * @return Attribute<string, string>
     */
    protected function whatsapp(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => self::normalizeWhatsapp($value),
        );
    }

    /**
     * Untuk dibaca manusia: 0812-3456-7890.
     */
    public function formattedWhatsapp(): string
    {
        return implode('-', array_filter([
            substr($this->whatsapp, 0, 4),
            substr($this->whatsapp, 4, 4),
            substr($this->whatsapp, 8),
        ]));
    }

    public static function findByWhatsapp(string $number): ?self
    {
        return self::query()->where('whatsapp', self::normalizeWhatsapp($number))->first();
    }

    /**
     * Paket "Kelas Pertama" hanya sekali per orang: belum pernah membelinya
     * dan belum pernah ikut kelas.
     */
    public function canUseIntroOffer(): bool
    {
        if (! $this->exists) {
            return true;
        }

        return $this->isFirstTimer()
            && ! $this->memberships()->whereHas('package', fn ($query) => $query->where('is_intro', true))->exists();
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
