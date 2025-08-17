<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Day: string implements HasLabel
{
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';

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
            self::MONDAY => 'Monday',
            self::TUESDAY => 'Tuesday',
            self::WEDNESDAY => 'Wednesday',
            self::THURSDAY => 'Thursday',
            self::FRIDAY => 'Friday',
            self::SATURDAY => 'Saturday',
            self::SUNDAY => 'Sunday',
        };
    }
}
