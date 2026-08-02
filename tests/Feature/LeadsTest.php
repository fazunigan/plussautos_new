<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\VehicleCondition;
use App\Models\Lead;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function tasacionValida(array $overrides = []): array
    {
        return [
            'name' => 'Camila Torres',
            'phone' => '+56 9 8765 4321',
            't_brand' => 'Toyota',
            't_model' => 'Corolla',
            't_version' => 'XEI 1.8',
            't_year' => 2018,
            't_mileage_km' => 84000,
            't_condition' => 'bueno',
            't_comuna' => 'Ñuñoa',
            ...$overrides,
        ];
    }

    public function test_el_formulario_de_tasacion_crea_el_lead(): void
    {
        $this->post(route('sell.store'), $this->tasacionValida())
            ->assertRedirect(route('sell.create'))
            ->assertSessionHas('status');

        $lead = Lead::sole();

        $this->assertSame(LeadType::Tasacion, $lead->type);
        $this->assertSame(LeadStatus::Nuevo, $lead->status);
        $this->assertSame('vende-tu-auto', $lead->source);
        $this->assertSame('Camila Torres', $lead->name);
        $this->assertSame('Toyota', $lead->t_brand);
        $this->assertSame('XEI 1.8', $lead->t_version);
        $this->assertSame(2018, $lead->t_year);
        $this->assertSame(VehicleCondition::Bueno, $lead->t_condition);
        $this->assertSame('Ñuñoa', $lead->t_comuna);
    }

    public function test_la_tasacion_exige_los_datos_del_auto(): void
    {
        $this->post(route('sell.store'), ['name' => 'Camila', 'phone' => '+56987654321'])
            ->assertSessionHasErrors(['t_brand', 't_model', 't_year', 't_mileage_km', 't_condition']);

        $this->assertSame(0, Lead::count());
    }

    public function test_el_estado_del_auto_debe_ser_uno_de_los_ofrecidos(): void
    {
        $this->post(route('sell.store'), $this->tasacionValida(['t_condition' => 'impecable']))
            ->assertSessionHasErrors('t_condition');

        $this->assertSame(0, Lead::count());
    }

    public function test_la_version_y_la_comuna_son_opcionales(): void
    {
        $datos = $this->tasacionValida();
        unset($datos['t_version'], $datos['t_comuna']);

        $this->post(route('sell.store'), $datos)->assertSessionHasNoErrors();

        $this->assertSame(1, Lead::count());
    }

    public function test_rechaza_un_telefono_que_no_parece_telefono(): void
    {
        $this->post(route('sell.store'), $this->tasacionValida(['phone' => '123']))
            ->assertSessionHasErrors('phone');

        $this->assertSame(0, Lead::count());
    }

    public function test_acepta_los_formatos_de_telefono_chilenos_habituales(): void
    {
        foreach (['+56 9 8765 4321', '56987654321', '9 8765 4321', '+569-8765-4321'] as $telefono) {
            $this->post(route('sell.store'), $this->tasacionValida(['phone' => $telefono]))
                ->assertSessionHasNoErrors();
        }

        $this->assertSame(4, Lead::count());
    }

    public function test_el_campo_trampa_bloquea_el_envio_automatizado(): void
    {
        $this->post(route('sell.store'), $this->tasacionValida(['website' => 'https://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Lead::count());
    }

    public function test_la_consulta_desde_una_ficha_queda_asociada_a_ese_auto(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->post(route('contact.store'), [
            'name' => 'Ignacio Rivas',
            'phone' => '+56912345678',
            'message' => '¿Sigue disponible?',
            'vehicle_id' => $vehicle->id,
        ])->assertRedirect(route('vehicles.show', $vehicle));

        $lead = Lead::sole();

        $this->assertSame(LeadType::Consulta, $lead->type);
        $this->assertSame($vehicle->id, $lead->vehicle_id);
        $this->assertSame('ficha', $lead->source);
    }

    public function test_la_consulta_general_no_queda_asociada_a_ningun_auto(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Ignacio Rivas',
            'phone' => '+56912345678',
            'message' => 'Busco una camioneta 4x4',
        ])->assertRedirect(route('contact.create'));

        $lead = Lead::sole();

        $this->assertNull($lead->vehicle_id);
        $this->assertSame('contacto', $lead->source);
    }
}
