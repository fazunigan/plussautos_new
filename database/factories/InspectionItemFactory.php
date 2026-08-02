<?php

namespace Database\Factories;

use App\Enums\InspectionCategory;
use App\Enums\InspectionStatus;
use App\Models\InspectionItem;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<InspectionItem> */
class InspectionItemFactory extends Factory
{
    protected $model = InspectionItem::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'category' => fake()->randomElement(InspectionCategory::cases()),
            'label' => fake()->words(3, true),
            'status' => InspectionStatus::Ok,
            'note' => null,
            'sort_order' => 0,
        ];
    }

    public function observation(): static
    {
        return $this->state(fn () => [
            'status' => InspectionStatus::Observacion,
            'note' => fake()->sentence(),
        ]);
    }
}
