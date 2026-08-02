<?php

namespace Database\Factories;

use App\Enums\BodyType;
use App\Enums\Fuel;
use App\Enums\Traction;
use App\Enums\Transmission;
use App\Enums\VehicleOrigin;
use App\Enums\VehicleStatus;
use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Vehicle> */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $brand = Brand::factory();

        return [
            'brand_id' => $brand,
            'vehicle_model_id' => VehicleModel::factory()->for($brand),
            'version' => fake()->randomElement(['GLS', 'GT', 'Limited', 'Sport', null]),
            'year' => fake()->numberBetween(2015, 2024),
            'price' => fake()->numberBetween(6, 26) * 500_000,
            'mileage_km' => fake()->numberBetween(15_000, 160_000),
            'transmission' => fake()->randomElement(Transmission::cases()),
            'fuel' => fake()->randomElement(Fuel::cases()),
            'body_type' => fake()->randomElement(BodyType::cases()),
            'engine_cc' => fake()->randomElement([1400, 1600, 2000, 2200, 2500]),
            'doors' => fake()->randomElement([4, 5]),
            'traction' => fake()->randomElement(Traction::cases()),
            'color' => fake()->randomElement(['Blanco', 'Gris', 'Negro', 'Rojo', 'Azul']),
            'owners_count' => fake()->numberBetween(1, 3),
            'description' => fake()->paragraph(),
            'status' => VehicleStatus::Available,
            'published_at' => fake()->dateTimeBetween('-4 months'),
            'featured' => false,
            'origin' => fake()->randomElement(VehicleOrigin::cases()),
            'plate' => strtoupper(fake()->bothify('??##??')),
            'location' => 'Santiago',
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => VehicleStatus::Draft, 'published_at' => null]);
    }

    public function reserved(): static
    {
        return $this->state(['status' => VehicleStatus::Reserved]);
    }

    public function sold(): static
    {
        return $this->state(fn () => [
            'status' => VehicleStatus::Sold,
            'sold_at' => now()->subDays(fake()->numberBetween(1, 60)),
        ]);
    }

    public function consignment(): static
    {
        return $this->state(fn () => [
            'origin' => VehicleOrigin::Consignment,
            'consignor_name' => fake()->name(),
            'consignor_phone' => '+569'.fake()->numerify('########'),
            'commission_amount' => fake()->numberBetween(300_000, 900_000),
        ]);
    }
}
