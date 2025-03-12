<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PostStatus: string implements HasLabel, HasColor
{
    case Active = 'active';
    case Archived = 'archived';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Archived => 'Archived',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'success',
            self::Archived => 'warning',
        };
    }
}
