<?php

namespace Tests\Feature;

use App\Models\InspectionItem;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiclePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_ficha_de_un_auto_disponible_se_muestra(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->get(route('vehicles.show', $vehicle))
            ->assertOk()
            ->assertSee($vehicle->brand->name)
            ->assertSee($vehicle->formattedPrice());
    }

    public function test_un_borrador_responde_404(): void
    {
        $vehicle = Vehicle::factory()->draft()->create();

        $this->get(route('vehicles.show', $vehicle))->assertNotFound();
    }

    public function test_un_auto_con_publicacion_futura_responde_404(): void
    {
        $vehicle = Vehicle::factory()->create(['published_at' => now()->addWeek()]);

        $this->get(route('vehicles.show', $vehicle))->assertNotFound();
    }

    /**
     * Los vendidos mantienen su URL viva porque ya tienen posicionamiento,
     * pero no deben ofrecer contacto de compra.
     */
    public function test_un_auto_vendido_sigue_accesible_pero_sin_contacto(): void
    {
        $vehicle = Vehicle::factory()->sold()->create();

        $this->get(route('vehicles.show', $vehicle))
            ->assertOk()
            ->assertSee('Vendido')
            ->assertDontSee('Escribir por WhatsApp');
    }

    public function test_la_hoja_de_inspeccion_se_publica_con_sus_observaciones(): void
    {
        $vehicle = Vehicle::factory()->create();

        InspectionItem::factory()->for($vehicle)->create([
            'category' => 'carroceria',
            'label' => 'Golpes y abolladuras',
            'status' => 'observacion',
            'note' => 'Raya de 8 cm en la puerta trasera derecha',
        ]);
        InspectionItem::factory()->for($vehicle)->create([
            'category' => 'motor',
            'label' => 'Nivel y estado del aceite',
            'status' => 'ok',
        ]);

        $this->get(route('vehicles.show', $vehicle))
            ->assertOk()
            ->assertSee('Hoja de inspección')
            ->assertSee('Raya de 8 cm en la puerta trasera derecha')
            ->assertSee('Nivel y estado del aceite');
    }

    public function test_la_ficha_publica_datos_estructurados_para_google(): void
    {
        $vehicle = Vehicle::factory()->create();

        $contenido = $this->get(route('vehicles.show', $vehicle))->assertOk()->getContent();

        $this->assertStringContainsString('application/ld+json', $contenido);
        $this->assertStringContainsString('"@type":"Vehicle"', $contenido);
        $this->assertStringContainsString('"priceCurrency":"CLP"', $contenido);
    }

    public function test_el_contador_de_detalles_cuenta_solo_lo_que_no_esta_conforme(): void
    {
        $vehicle = Vehicle::factory()->create();

        InspectionItem::factory()->count(3)->for($vehicle)->create(['status' => 'ok']);
        InspectionItem::factory()->count(2)->for($vehicle)->observation()->create();

        $this->assertSame(2, $vehicle->fresh()->documentedDetailsCount());
    }
}
