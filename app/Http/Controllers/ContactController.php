<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Http\Requests\StoreInquiryRequest;
use App\Models\Lead;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact');
    }

    public function store(StoreInquiryRequest $request): RedirectResponse
    {
        $lead = Lead::create([
            ...$request->safe()->except('website'),
            'type' => LeadType::Consulta,
            'status' => LeadStatus::Nuevo,
            'source' => $request->filled('vehicle_id') ? 'ficha' : 'contacto',
        ]);

        $message = 'Recibimos tu mensaje. Te respondemos a la brevedad.';

        if ($lead->vehicle_id !== null) {
            return redirect()
                ->route('vehicles.show', $lead->vehicle)
                ->with('status', $message);
        }

        return redirect()->route('contact.create')->with('status', $message);
    }
}
