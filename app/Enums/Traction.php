<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Traction: string implements HasLabel
{
    case FourByTwo = '4x2';
    case FourByFour = '4x4';
    case Awd = 'awd';

    public function label(): string
    {
        return match ($this) {
            self::FourByTwo => '4x2',
            self::FourByFour => '4x4',
            self::Awd => 'AWD',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
