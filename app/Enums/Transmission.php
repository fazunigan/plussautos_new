<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Transmission: string implements HasLabel
{
    case Manual = 'manual';
    case Automatica = 'automatica';
    case Cvt = 'cvt';

    /** En Chile "mecánica" es el término corriente para la caja manual. */
    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Mecánica',
            self::Automatica => 'Automática',
            self::Cvt => 'CVT',
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
