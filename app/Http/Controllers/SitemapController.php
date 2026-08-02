<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Response;

/**
 * Se genera al vuelo en vez de por comando programado: el catálogo cambia
 * varias veces por semana y un sitemap desactualizado le cuesta indexación
 * a un negocio que depende de Google.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('vehicles.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('sell.create'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('inspection.create'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('contact.create'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('terms'), 'changefreq' => 'yearly', 'priority' => '0.2'],
            ['loc' => route('privacy'), 'changefreq' => 'yearly', 'priority' => '0.2'],
        ];

        Vehicle::query()
            ->publiclyVisible()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at', 'status'])
            ->each(function (Vehicle $vehicle) use (&$urls): void {
                $urls[] = [
                    'loc' => route('vehicles.show', $vehicle),
                    'lastmod' => $vehicle->updated_at?->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
