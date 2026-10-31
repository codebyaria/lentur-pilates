<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Level kelas: bahasa pertemanan, bukan peringkat.
 */
enum ClassLevel: string implements HasColor, HasLabel
{
    case BaruKenal = 'baru_kenal';
    case MulaiAkrab = 'mulai_akrab';
    case SudahLengket = 'sudah_lengket';

    public function getLabel(): string
    {
        return match ($this) {
            self::BaruKenal => __('Baru Kenal'),
            self::MulaiAkrab => __('Mulai Akrab'),
            self::SudahLengket => __('Sudah Lengket'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::BaruKenal => 'success',
            self::MulaiAkrab => 'warning',
            self::SudahLengket => 'primary',
        };
    }
}
