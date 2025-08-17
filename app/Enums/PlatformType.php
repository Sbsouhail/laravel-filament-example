<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PlatformType: string implements HasLabel
{
    case WEB = 'web';
    case IOS = 'ios';
    case ANDROID = 'android';

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
            self::WEB => 'Web',
            self::IOS => 'iOS',
            self::ANDROID => 'Android',
        };
    }
}
