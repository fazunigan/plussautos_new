<?php

namespace App\Http\Controllers;

use App\Enums\BodyType;
use App\Enums\Fuel;
use App\Enums\Transmission;
use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Support\VehicleFilter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $filter = VehicleFilter::fromRequest($request);

        $vehicles = $filter
            ->apply(
                Vehicle::query()
                    ->listable()
                    ->with(['brand', 'vehicleModel', 'media'])
                    ->withCount(['inspectionItems as documented_details_count' => fn ($query) => $query->details()])
            )
            ->paginate(config('pluss.per_page'))
            ->withQueryString();

        return view('vehicles.index', [
            'vehicles' => $vehicles,
            'filter' => $filter,
            'brands' => Brand::query()->orderBy('name')->get(),
            'models' => $this->modelsForBrand($filter->marca),
            'transmissions' => Transmission::options(),
            'fuels' => Fuel::options(),
            'bodyTypes' => BodyType::options(),
        ]);
    }

    public function show(Vehicle $vehicle): View
    {
        abort_unless($this->isPubliclyVisible($vehicle), 404);

        $vehicle->load(['brand', 'vehicleModel', 'media', 'inspectionItems.media']);

        $similar = Vehicle::query()
            ->listable()
            ->whereKeyNot($vehicle->getKey())
            ->where('body_type', $vehicle->body_type->value)
            ->whereBetween('price', [(int) ($vehicle->price * 0.7), (int) ($vehicle->price * 1.3)])
            ->with(['brand', 'vehicleModel', 'media'])
            ->withCount(['inspectionItems as documented_details_count' => fn ($query) => $query->details()])
            ->limit(3)
            ->get();

        return view('vehicles.show', [
            'vehicle' => $vehicle,
            'similar' => $similar,
        ]);
    }

    /**
     * Los borradores nunca son accesibles. Los vendidos sí: su URL ya tiene
     * posicionamiento en Google y la página sirve de prueba social.
     */
    private function isPubliclyVisible(Vehicle $vehicle): bool
    {
        return in_array($vehicle->status, \App\Enums\VehicleStatus::publiclyVisible(), true)
            && $vehicle->published_at !== null
            && $vehicle->published_at->isPast();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, VehicleModel> */
    private function modelsForBrand(?string $brandSlug): \Illuminate\Database\Eloquent\Collection
    {
        if ($brandSlug === null) {
            return VehicleModel::query()->whereRaw('1 = 0')->get();
        }

        return VehicleModel::query()
            ->whereHas('brand', fn ($query) => $query->where('slug', $brandSlug))
            ->orderBy('name')
            ->get();
    }
}
