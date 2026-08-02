<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\InspectionItem;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $latest = Vehicle::query()
            ->listable()
            ->with(['brand', 'vehicleModel', 'media'])
            ->withCount(['inspectionItems as documented_details_count' => fn ($query) => $query->details()])
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit(config('pluss.home_featured'))
            ->get();

        $sample = $this->sampleVehicle();

        return view('home', [
            'latest' => $latest,
            'sample' => $sample,
            'sampleRows' => $this->rowsFor($sample),
            // Para el buscador del héroe: solo marcas que tienen algo publicado,
            // porque ofrecer un filtro que devuelve cero resultados es peor que
            // no ofrecerlo.
            'brands' => Brand::query()
                ->whereHas('vehicles', fn ($query) => $query->listable())
                ->orderBy('name')
                ->get(),
            'availableCount' => Vehicle::query()->listable()->count(),
        ]);
    }

    /**
     * El auto que se muestra como ejemplo de hoja de inspección. Se elige el
     * que tiene más observaciones documentadas: una hoja donde todo sale
     * conforme no prueba nada, que es justo lo contrario de lo que el sitio
     * quiere demostrar.
     */
    private function sampleVehicle(): ?Vehicle
    {
        return Vehicle::query()
            ->listable()
            ->has('inspectionItems')
            ->withCount(['inspectionItems as documented_details_count' => fn ($query) => $query->details()])
            ->with(['brand', 'vehicleModel', 'inspectionItems'])
            ->orderByDesc('documented_details_count')
            ->first();
    }

    /**
     * Filas planas listas para el navegador, ordenadas por categoría y con las
     * observaciones primero dentro de cada una.
     *
     * @return Collection<int, array<string, string|null>>
     */
    private function rowsFor(?Vehicle $vehicle): Collection
    {
        if ($vehicle === null) {
            return collect();
        }

        // Las observaciones van primero, no en el orden del documento. Abrir con
        // ocho filas seguidas de "Conforme" no muestra nada; lo que hay que ver
        // es justamente lo que el auto tiene.
        return $vehicle->inspectionItems
            ->sortBy(fn (InspectionItem $item) => [
                $item->status->isDetail() ? 0 : 1,
                $item->category->order(),
                $item->sort_order,
            ])
            ->map(fn (InspectionItem $item) => [
                'cat' => $item->category->value,
                'catLabel' => $item->category->label(),
                // Las filas van con las observaciones primero, pero las pastillas
                // de categoría conservan el orden del documento.
                'catOrder' => $item->category->order(),
                'label' => $item->label,
                'status' => $item->status->value,
                'statusLabel' => $item->status->label(),
                'note' => $item->note,
            ])
            ->values();
    }
}
