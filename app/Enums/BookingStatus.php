<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BookingStatus: string implements HasColor, HasLabel
{
    case Confirmed = 'confirmed';
    case Attended = 'attended';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    /**
     * Status yang memakai satu tempat di kelas.
     *
     * @return list<self>
     */
    public static function holdingSpot(): array
    {
        return [self::Confirmed, self::Attended, self::NoShow];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Confirmed => __('Terdaftar'),
            self::Attended => __('Hadir'),
            self::Cancelled => __('Batal'),
            self::NoShow => __('Tidak datang'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Confirmed => 'primary',
            self::Attended => 'success',
            self::Cancelled => 'gray',
            self::NoShow => 'danger',
        };
    }
}
