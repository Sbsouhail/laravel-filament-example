<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasLabel
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
    case UNDEFINED = 'undefined';

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
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::OTHER => 'Other',
            self::UNDEFINED => 'Prefer not to say',
        };
    }
}
