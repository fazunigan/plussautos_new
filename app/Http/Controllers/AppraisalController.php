<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Http\Requests\StoreAppraisalRequest;
use App\Models\Lead;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AppraisalController extends Controller
{
    public function create(): View
    {
        return view('sell');
    }

    public function store(StoreAppraisalRequest $request): RedirectResponse
    {
        Lead::create([
            ...$request->safe()->except('website'),
            'type' => LeadType::Tasacion,
            'status' => LeadStatus::Nuevo,
            'source' => 'vende-tu-auto',
        ]);

        return redirect()
            ->route('sell.create')
            ->with('status', 'Recibimos los datos de tu auto. Te contactamos con una oferta dentro del día hábil siguiente.');
    }
}
