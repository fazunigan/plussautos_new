<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** Estado que declara el dueño en el cotizador. */
enum VehicleCondition: string implements HasLabel
{
    case Excelente = 'excelente';
    case Bueno = 'bueno';
    case Regular = 'regular';

    public function label(): string
    {
        return match ($this) {
            self::Excelente => 'Excelente',
            self::Bueno => 'Bueno',
            self::Regular => 'Regular',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Excelente => 'Sin golpes ni rayas, mantenciones al día',
            self::Bueno => 'Marcas de uso normales para su año',
            self::Regular => 'Tiene detalles que habría que reparar',
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
