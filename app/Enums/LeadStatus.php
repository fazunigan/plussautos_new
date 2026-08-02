<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasColor, HasLabel
{
    case Nuevo = 'nuevo';
    case Contactado = 'contactado';
    case EnNegociacion = 'en_negociacion';
    case Cerrado = 'cerrado';
    case Descartado = 'descartado';

    public function label(): string
    {
        return match ($this) {
            self::Nuevo => 'Nuevo',
            self::Contactado => 'Contactado',
            self::EnNegociacion => 'En negociación',
            self::Cerrado => 'Cerrado',
            self::Descartado => 'Descartado',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Nuevo => 'warning',
            self::Contactado => 'info',
            self::EnNegociacion => 'primary',
            self::Cerrado => 'success',
            self::Descartado => 'gray',
        };
    }
}
