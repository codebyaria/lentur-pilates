<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Carbon\CarbonImmutable;
use Database\Factories\MembershipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Paket yang sudah dibeli member, dengan sisa kelasnya.
 *
 * @property int $id
 * @property int $member_id
 * @property int $package_id
 * @property int $classes_total
 * @property int $classes_remaining
 * @property int $price_paid
 * @property PaymentStatus $payment_status
 * @property string $payment_reference
 * @property CarbonImmutable $purchased_at
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Member $member
 * @property-read Package $package
 */
#[Fillable(['member_id', 'package_id', 'classes_total', 'classes_remaining', 'price_paid', 'payment_status', 'payment_reference', 'purchased_at', 'expires_at'])]
class Membership extends Model
{
    /** @use HasFactory<MembershipFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Member, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return BelongsTo<Package, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isUsable(): bool
    {
        return $this->payment_status === PaymentStatus::Paid
            && $this->classes_remaining > 0
            && $this->expires_at->isFuture();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payment_status' => PaymentStatus::class,
            'purchased_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
