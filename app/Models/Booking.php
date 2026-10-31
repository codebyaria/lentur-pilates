<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\CarbonImmutable;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $code
 * @property int $class_session_id
 * @property int $member_id
 * @property int|null $membership_id
 * @property BookingStatus $status
 * @property bool $is_first_class
 * @property CarbonImmutable|null $cancelled_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read ClassSession $classSession
 * @property-read Member $member
 * @property-read Membership|null $membership
 */
#[Fillable(['code', 'class_session_id', 'member_id', 'membership_id', 'status', 'is_first_class', 'cancelled_at'])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            $booking->code ??= self::generateCode();
        });
    }

    public static function generateCode(): string
    {
        do {
            $code = 'LTR-'.Str::upper(Str::random(6));
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    /**
     * @return BelongsTo<ClassSession, $this>
     */
    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    /**
     * @return BelongsTo<Member, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return BelongsTo<Membership, $this>
     */
    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    /**
     * Batas batal gratis: sekian jam sebelum kelas mulai (config/lentur.php).
     */
    public function freeCancellationDeadline(): CarbonImmutable
    {
        return $this->classSession->starts_at
            ->copy()
            ->subHours((int) config('lentur.free_cancellation_hours'));
    }

    public function canCancelForFree(): bool
    {
        return $this->status === BookingStatus::Confirmed
            && now()->lessThanOrEqualTo($this->freeCancellationDeadline());
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function holdingSpot(Builder $query): void
    {
        $query->whereIn('status', BookingStatus::holdingSpot());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'is_first_class' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }
}
