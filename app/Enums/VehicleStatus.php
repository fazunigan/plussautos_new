<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum VehicleStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::Available => 'Disponible',
            self::Reserved => 'Reservado',
            self::Sold => 'Vendido',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Available => 'success',
            self::Reserved => 'warning',
            self::Sold => 'danger',
        };
    }

    /**
     * Estados que el público puede ver listados en el catálogo.
     *
     * @return array<int, self>
     */
    public static function listable(): array
    {
        return [self::Available, self::Reserved];
    }

    /**
     * Estados con página pública accesible. Los vendidos mantienen su URL viva:
     * ya tienen posicionamiento en Google y sirven de prueba social.
     *
     * @return array<int, self>
     */
    public static function publiclyVisible(): array
    {
        return [self::Available, self::Reserved, self::Sold];
    }

    /** Solo el estado disponible o reservado admite contacto de compra. */
    public function acceptsContact(): bool
    {
        return in_array($this, [self::Available, self::Reserved], true);
    }
}
