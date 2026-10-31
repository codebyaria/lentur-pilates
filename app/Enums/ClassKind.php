<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClassKind: string implements HasLabel
{
    case Mat = 'mat';
    case Reformer = 'reformer';

    public function getLabel(): string
    {
        return match ($this) {
            self::Mat => __('Mat'),
            self::Reformer => __('Reformer'),
        };
    }
}
