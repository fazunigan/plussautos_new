<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadType: string implements HasLabel
{
    case Consulta = 'consulta';
    case Tasacion = 'tasacion';
    case Revision = 'revision';

    public function label(): string
    {
        return match ($this) {
            self::Consulta => 'Consulta',
            self::Tasacion => 'Tasación',
            self::Revision => 'Revisión precompra',
        };
    }

    /** Los que traen datos de un vehículo que no es nuestro. */
    public function aboutExternalVehicle(): bool
    {
        return in_array($this, [self::Tasacion, self::Revision], true);
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
