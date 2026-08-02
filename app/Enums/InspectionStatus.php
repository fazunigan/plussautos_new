<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InspectionStatus: string implements HasColor, HasLabel
{
    case Ok = 'ok';
    case Observacion = 'observacion';
    case Reparado = 'reparado';

    public function label(): string
    {
        return match ($this) {
            self::Ok => 'Conforme',
            self::Observacion => 'Con observación',
            self::Reparado => 'Reparado',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Ok => 'success',
            self::Observacion => 'warning',
            self::Reparado => 'info',
        };
    }

    /** Los ítems que no están conformes son los "detalles documentados". */
    public function isDetail(): bool
    {
        return $this !== self::Ok;
    }
}
