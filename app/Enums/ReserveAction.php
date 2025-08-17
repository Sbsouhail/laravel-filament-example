<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReserveAction: string implements HasLabel
{
    case WEBSITE = 'website';
    case CALL = 'call';

    /** @return string[] */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function count(): int
    {
        return count(self::cases());
    }

    public function getLabel(): string
    {
        // return $this->name;

        // or

        return match ($this) {
            self::WEBSITE => 'Website',
            self::CALL => 'Call',
        };
    }
}
