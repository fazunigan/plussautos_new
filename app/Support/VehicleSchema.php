<?php

namespace App\Support;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;

/**
 * Datos estructurados schema.org para la ficha de un vehículo.
 *
 * Vive fuera de la vista porque las claves '@context' y '@type' rompen el
 * compilador de Blade cuando el array se escribe dentro de una directiva,
 * y porque el marcado para buscadores es lógica, no presentación.
 */
final class VehicleSchema
{
    public static function for(Vehicle $vehicle): string
    {
        $data = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Vehicle',
            'name' => $vehicle->fullTitle(),
            'description' => $vehicle->description,
            'brand' => $vehicle->brand
                ? ['@type' => 'Brand', 'name' => $vehicle->brand->name]
                : null,
            'model' => $vehicle->vehicleModel?->name,
            'vehicleModelDate' => (string) $vehicle->year,
            'productionDate' => (string) $vehicle->year,
            'mileageFromOdometer' => [
                '@type' => 'QuantitativeValue',
                'value' => $vehicle->mileage_km,
                'unitCode' => 'KMT',
            ],
            'vehicleTransmission' => $vehicle->transmission->label(),
            'fuelType' => $vehicle->fuel->label(),
            'bodyType' => $vehicle->body_type->label(),
            'color' => $vehicle->color,
            'numberOfDoors' => $vehicle->doors,
            'numberOfPreviousOwners' => $vehicle->owners_count,
            'driveWheelConfiguration' => $vehicle->traction?->label(),
            'vehicleEngine' => $vehicle->engine_cc ? [
                '@type' => 'EngineSpecification',
                'engineDisplacement' => [
                    '@type' => 'QuantitativeValue',
                    'value' => $vehicle->engine_cc,
                    'unitCode' => 'CMQ',
                ],
            ] : null,
            'image' => $vehicle->coverUrl('full'),
            'url' => route('vehicles.show', $vehicle),
            'itemCondition' => 'https://schema.org/UsedCondition',
            'offers' => [
                '@type' => 'Offer',
                'price' => $vehicle->price,
                'priceCurrency' => 'CLP',
                'availability' => self::availability($vehicle),
                'itemCondition' => 'https://schema.org/UsedCondition',
                'url' => route('vehicles.show', $vehicle),
                'seller' => ['@type' => 'AutoDealer', 'name' => 'Pluss Autos'],
            ],
        ], fn ($value) => $value !== null && $value !== '');

        return (string) json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );
    }

    private static function availability(Vehicle $vehicle): string
    {
        return match ($vehicle->status) {
            VehicleStatus::Sold => 'https://schema.org/SoldOut',
            VehicleStatus::Reserved => 'https://schema.org/LimitedAvailability',
            default => 'https://schema.org/InStock',
        };
    }
}
