<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadType: string implements HasLabel
{
    case Consulta = 'consulta';
    case Tasacion = 'tasacion';

    public function label(): string
    {
        return match ($this) {
            self::Consulta => 'Consulta',
            self::Tasacion => 'Tasación',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
