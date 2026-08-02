<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\VehicleCondition;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Lead> */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'type' => LeadType::Consulta,
            'name' => fake()->name(),
            'phone' => '+569'.fake()->numerify('########'),
            'email' => fake()->safeEmail(),
            'message' => fake()->sentence(),
            'status' => LeadStatus::Nuevo,
            'source' => 'contacto',
        ];
    }

    public function appraisal(): static
    {
        return $this->state(fn () => [
            'type' => LeadType::Tasacion,
            'source' => 'vende-tu-auto',
            't_brand' => fake()->randomElement(['Toyota', 'Mazda', 'Hyundai', 'Kia']),
            't_model' => fake()->randomElement(['Corolla', 'CX-5', 'Tucson', 'Rio']),
            't_year' => fake()->numberBetween(2012, 2023),
            't_mileage_km' => fake()->numberBetween(20_000, 180_000),
            't_condition' => fake()->randomElement(VehicleCondition::cases()),
            't_comuna' => fake()->randomElement(['Ñuñoa', 'Maipú', 'La Florida', 'Providencia']),
            't_plate' => strtoupper(fake()->bothify('??##??')),
        ]);
    }
}
