<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OfferType: string implements HasLabel
{
    case EVERYDAY = 'everyday';
    case WORKDAYS = 'workdays';
    case LUNCH = 'lunch';

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
            self::EVERYDAY => 'Everyday',
            self::WORKDAYS => 'Workdays',
            self::LUNCH => 'Lunch',
        };
    }
}
