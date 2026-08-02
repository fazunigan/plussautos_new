<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<VehicleModel> */
class VehicleModelFactory extends Factory
{
    protected $model = VehicleModel::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'brand_id' => Brand::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
        ];
    }
}
