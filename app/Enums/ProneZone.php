<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProneZone: string implements HasLabel, HasColor
{
    case One = 'krb-i';
    case Two = 'krb-ii';
    case Three = 'krb-iii';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::One => 'KRB I',
            self::Two => 'KRB II',
            self::Three => 'KRB III',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::One => Color::Yellow,
            self::Two => Color::Rose,
            self::Three => Color::Red,
        };
    }
}
