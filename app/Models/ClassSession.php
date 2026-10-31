<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\ClassSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu kelas terjadwal: jenis kelas + instruktur + jam.
 *
 * @property int $id
 * @property int $class_type_id
 * @property int $instructor_id
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable $ends_at
 * @property int|null $capacity
 * @property string $room
 * @property CarbonImmutable|null $cancelled_at
 * @property int|null $active_bookings_count
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read ClassType $classType
 * @property-read Instructor $instructor
 */
#[Fillable(['class_type_id', 'instructor_id', 'starts_at', 'ends_at', 'capacity', 'room', 'cancelled_at'])]
class ClassSession extends Model
{
    /** @use HasFactory<ClassSessionFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        // Jam selesai selalu mengikuti durasi jenis kelas.
        static::saving(function (ClassSession $session) {
            if ($session->isDirty(['starts_at', 'class_type_id'])) {
                $duration = ClassType::query()->whereKey($session->class_type_id)->value('duration_minutes') ?? 50;
                $session->ends_at = $session->starts_at->copy()->addMinutes((int) $duration);
            }
        });
    }

    /**
     * @return BelongsTo<ClassType, $this>
     */
    public function classType(): BelongsTo
    {
        return $this->belongsTo(ClassType::class);
    }

    /**
     * @return BelongsTo<Instructor, $this>
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Booking yang memakai tempat (tidak termasuk yang batal).
     *
     * @return HasMany<Booking, $this>
     */
    public function activeBookings(): HasMany
    {
        return $this->bookings()->whereIn('status', BookingStatus::holdingSpot());
    }

    public function effectiveCapacity(): int
    {
        return $this->capacity ?? $this->classType->capacity;
    }

    public function bookedCount(): int
    {
        return $this->active_bookings_count ?? $this->activeBookings()->count();
    }

    public function spotsLeft(): int
    {
        return max(0, $this->effectiveCapacity() - $this->bookedCount());
    }

    public function isFull(): bool
    {
        return $this->spotsLeft() === 0;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function isBookable(): bool
    {
        return ! $this->isCancelled()
            && $this->starts_at->isFuture()
            && ! $this->isFull();
    }

    /**
     * "Kelas ini sudah ramai. Jam berikutnya masih lega."
     *
     * Jadwal lain yang masih ada tempat, level yang sama, dalam 7 hari ke depan.
     * Jenis kelas yang sama didahulukan, lalu yang paling cepat.
     */
    public function nextAvailableAlternative(): ?self
    {
        return self::query()
            ->with(['classType', 'instructor'])
            ->withSpotCounts()
            ->bookable()
            ->whereKeyNot($this->getKey())
            ->whereHas('classType', fn (Builder $query) => $query->where('level', $this->classType->level))
            ->whereBetween('starts_at', [
                $this->starts_at->copy()->max(now()),
                $this->starts_at->copy()->addDays(7),
            ])
            ->orderBy('starts_at')
            ->get()
            ->filter(fn (self $session) => ! $session->isFull())
            ->sortBy(fn (self $session) => [
                $session->class_type_id === $this->class_type_id ? 0 : 1,
                $session->starts_at->getTimestamp(),
            ])
            ->first();
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function withSpotCounts(Builder $query): void
    {
        $query->withCount('activeBookings');
    }

    /**
     * Belum mulai dan tidak dibatalkan (penuh atau tidak).
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function bookable(Builder $query): void
    {
        $query->whereNull('cancelled_at')->where('starts_at', '>', now());
    }

    /**
     * Senin–Minggu dari tanggal yang diberikan (default: minggu ini).
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function inWeekOf(Builder $query, ?CarbonInterface $date = null): void
    {
        $query->whereBetween('starts_at', self::weekRange($date ?? now()))->orderBy('starts_at');
    }

    /**
     * Senin 00:00 sampai Minggu 23:59 dari minggu tanggal tersebut.
     *
     * @return array{CarbonInterface, CarbonInterface}
     */
    public static function weekRange(CarbonInterface $date): array
    {
        return [
            $date->copy()->startOfWeek(CarbonInterface::MONDAY),
            $date->copy()->endOfWeek(CarbonInterface::SUNDAY),
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }
}
