<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Fuel: string implements HasLabel
{
    case Bencina = 'bencina';
    case Diesel = 'diesel';
    case Hibrido = 'hibrido';
    case Electrico = 'electrico';
    case Gas = 'gas';

    public function label(): string
    {
        return match ($this) {
            self::Bencina => 'Bencina',
            self::Diesel => 'Diésel',
            self::Hibrido => 'Híbrido',
            self::Electrico => 'Eléctrico',
            self::Gas => 'Gas',
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
