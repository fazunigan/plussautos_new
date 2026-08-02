<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** Interno. Nunca se expone en el sitio público. */
enum VehicleOrigin: string implements HasLabel
{
    case Own = 'own';
    case Consignment = 'consignment';

    public function label(): string
    {
        return match ($this) {
            self::Own => 'Stock propio',
            self::Consignment => 'Consignación',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
