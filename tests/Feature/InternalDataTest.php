<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La prueba que más importa del proyecto. Una filtración del precio de compra
 * o de los datos del consignante no es un error cosmético: es un problema
 * comercial serio con el dueño del auto y con el comprador.
 */
class InternalDataTest extends TestCase
{
    use RefreshDatabase;

    private function vehicleConDatosInternos(): Vehicle
    {
        return Vehicle::factory()->consignment()->create([
            'plate' => 'JHZK42',
            'consignor_name' => 'Rodrigo Mellado',
            'consignor_phone' => '+56911112222',
            'purchase_price' => 9_100_000,
            'commission_amount' => 650_000,
            'location' => 'Bodega Quilicura, pasillo 3',
            'internal_notes' => 'Ojo: el dueño no quiere bajar de 15 millones',
            'price' => 16_000_000,
        ]);
    }

    /** @return list<string> */
    private function valoresInternos(Vehicle $vehicle): array
    {
        return [
            $vehicle->plate,
            $vehicle->consignor_name,
            $vehicle->consignor_phone,
            (string) $vehicle->purchase_price,
            number_format((int) $vehicle->purchase_price, 0, ',', '.'),
            (string) $vehicle->commission_amount,
            $vehicle->location,
            $vehicle->internal_notes,
        ];
    }

    public function test_la_ficha_publica_no_expone_ningun_dato_interno(): void
    {
        $vehicle = $this->vehicleConDatosInternos();

        $contenido = $this->get(route('vehicles.show', $vehicle))->assertOk()->getContent();

        foreach ($this->valoresInternos($vehicle) as $valor) {
            $this->assertStringNotContainsString(
                $valor,
                $contenido,
                "El dato interno «{$valor}» apareció en la ficha pública.",
            );
        }
    }

    public function test_el_catalogo_no_expone_ningun_dato_interno(): void
    {
        $vehicle = $this->vehicleConDatosInternos();

        $contenido = $this->get(route('vehicles.index'))->assertOk()->getContent();

        foreach ($this->valoresInternos($vehicle) as $valor) {
            $this->assertStringNotContainsString($valor, $contenido);
        }
    }

    public function test_el_modelo_oculta_los_campos_internos_al_serializar(): void
    {
        $serializado = $this->vehicleConDatosInternos()->toArray();

        foreach ([
            'plate',
            'consignor_name',
            'consignor_phone',
            'purchase_price',
            'commission_amount',
            'location',
            'internal_notes',
        ] as $campo) {
            $this->assertArrayNotHasKey($campo, $serializado);
        }
    }

    public function test_el_origen_del_vehiculo_no_se_muestra_al_publico(): void
    {
        $vehicle = $this->vehicleConDatosInternos();

        $this->get(route('vehicles.show', $vehicle))
            ->assertOk()
            ->assertDontSee('Consignación')
            ->assertDontSee('Stock propio');
    }
}
