<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BodyType: string implements HasLabel
{
    case Sedan = 'sedan';
    case Suv = 'suv';
    case Hatchback = 'hatchback';
    case Camioneta = 'camioneta';
    case StationWagon = 'station_wagon';
    case Coupe = 'coupe';
    case Van = 'van';

    public function label(): string
    {
        return match ($this) {
            self::Sedan => 'Sedán',
            self::Suv => 'SUV',
            self::Hatchback => 'Hatchback',
            self::Camioneta => 'Camioneta',
            self::StationWagon => 'Station Wagon',
            self::Coupe => 'Coupé',
            self::Van => 'Van',
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
