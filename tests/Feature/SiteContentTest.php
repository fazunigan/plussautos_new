<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Decisión del negocio: el sitio no menciona que no haya un punto de venta
     * físico. Ofrecer llevar el auto es un beneficio de servicio y se queda;
     * anunciar la ausencia de local, no.
     *
     * @return list<string>
     */
    private function frasesProhibidas(): array
    {
        return [
            'local a la calle',
            'sin local',
            'no tenemos local',
            'punto físico',
            'showroom',
            'no tenemos sucursal',
            'sin sucursal',
            'vitrina',
        ];
    }

    /** @return list<string> */
    private function rutasPublicas(): array
    {
        $vehicle = Vehicle::factory()->create();

        return [
            route('home'),
            route('vehicles.index'),
            route('vehicles.show', $vehicle),
            route('sell.create'),
            route('contact.create'),
            route('about'),
            route('terms'),
            route('privacy'),
        ];
    }

    public function test_ninguna_pagina_menciona_la_falta_de_local_fisico(): void
    {
        foreach ($this->rutasPublicas() as $url) {
            $contenido = mb_strtolower($this->get($url)->assertOk()->getContent());

            foreach ($this->frasesProhibidas() as $frase) {
                $this->assertStringNotContainsString(
                    $frase,
                    $contenido,
                    "La frase «{$frase}» aparece en {$url}.",
                );
            }
        }
    }

    public function test_la_direccion_solo_aparece_si_esta_cargada(): void
    {
        $this->get(route('contact.create'))
            ->assertOk()
            ->assertDontSee('Dirección');

        SiteSetting::current()->update([
            'address' => 'Av. Providencia 1234, Providencia',
            'hours' => 'Lunes a viernes de 10 a 19 h',
        ]);

        $this->get(route('contact.create'))
            ->assertOk()
            ->assertSee('Av. Providencia 1234, Providencia')
            ->assertSee('Lunes a viernes de 10 a 19 h');
    }

    public function test_el_cotizador_esta_en_la_portada_y_en_su_pagina(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Cotiza tu auto online en minutos')
            ->assertSee('id="cotizador"', escape: false)
            ->assertSee(route('sell.store'), escape: false);

        $this->get(route('sell.create'))
            ->assertOk()
            ->assertSee('Cotiza tu auto online en minutos')
            ->assertSee('¿Cómo está el auto?');
    }

    /**
     * Un auto sin fotos es un caso real mientras se carga el reportaje.
     * El espacio se llena con la marca, no con un hueco gris.
     */
    public function test_un_auto_sin_fotos_muestra_el_marcador_con_la_marca(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->get(route('vehicles.show', $vehicle))
            ->assertOk()
            ->assertSee('Fotos en camino')
            ->assertSee('img/mark.webp', escape: false);

        $this->get(route('vehicles.index'))
            ->assertOk()
            ->assertSee('img/mark.webp', escape: false);
    }
}
