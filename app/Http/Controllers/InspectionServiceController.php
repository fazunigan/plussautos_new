<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Http\Requests\StoreInspectionRequest;
use App\Models\Lead;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Revisión precompra: el cliente encontró un auto en otra parte y nos contrata
 * para revisarlo antes de comprarlo. Es la misma pauta que aplicamos a nuestro
 * propio stock, vendida como servicio.
 */
class InspectionServiceController extends Controller
{
    public function create(): View
    {
        return view('inspection');
    }

    public function store(StoreInspectionRequest $request): RedirectResponse
    {
        Lead::create([
            ...$request->safe()->except('website'),
            'type' => LeadType::Revision,
            'status' => LeadStatus::Nuevo,
            'source' => 'revision-precompra',
        ]);

        return redirect()
            ->route('inspection.create')
            ->with('status', 'Recibimos tu solicitud. Te escribimos para coordinar la revisión y confirmarte el valor.');
    }
}
