<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InspectionCategory: string implements HasLabel
{
    case Motor = 'motor';
    case Transmision = 'transmision';
    case Frenos = 'frenos';
    case Suspension = 'suspension';
    case Neumaticos = 'neumaticos';
    case Carroceria = 'carroceria';
    case Interior = 'interior';
    case Documentacion = 'documentacion';

    public function label(): string
    {
        return match ($this) {
            self::Motor => 'Motor',
            self::Transmision => 'Transmisión',
            self::Frenos => 'Frenos',
            self::Suspension => 'Suspensión',
            self::Neumaticos => 'Neumáticos',
            self::Carroceria => 'Carrocería',
            self::Interior => 'Interior',
            self::Documentacion => 'Documentación',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    /** Orden en que se presentan las categorías en la ficha. */
    public function order(): int
    {
        return match ($this) {
            self::Motor => 1,
            self::Transmision => 2,
            self::Frenos => 3,
            self::Suspension => 4,
            self::Neumaticos => 5,
            self::Carroceria => 6,
            self::Interior => 7,
            self::Documentacion => 8,
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
