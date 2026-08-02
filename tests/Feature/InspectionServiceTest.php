<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspectionServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function solicitudValida(array $overrides = []): array
    {
        return [
            'name' => 'Rodrigo Mellado',
            'phone' => '+56 9 8765 4321',
            'email' => 'rodrigo@example.com',
            't_brand' => 'Mazda',
            't_model' => 'CX-5',
            't_year' => 2019,
            't_comuna' => 'Ñuñoa',
            't_listing_url' => 'https://www.chileautos.cl/aviso/12345',
            ...$overrides,
        ];
    }

    public function test_la_pagina_del_servicio_se_muestra(): void
    {
        $this->get(route('inspection.create'))
            ->assertOk()
            ->assertSee('Revisión precompra')
            ->assertSee('Qué revisamos')
            ->assertSee('Pedir la revisión');
    }

    public function test_la_solicitud_crea_un_lead_de_revision(): void
    {
        $this->post(route('inspection.store'), $this->solicitudValida())
            ->assertRedirect(route('inspection.create'))
            ->assertSessionHas('status');

        $lead = Lead::sole();

        $this->assertSame(LeadType::Revision, $lead->type);
        $this->assertSame(LeadStatus::Nuevo, $lead->status);
        $this->assertSame('revision-precompra', $lead->source);
        $this->assertSame('Mazda', $lead->t_brand);
        $this->assertSame('Ñuñoa', $lead->t_comuna);
        $this->assertSame('https://www.chileautos.cl/aviso/12345', $lead->t_listing_url);
    }

    public function test_exige_saber_cual_es_el_auto_y_donde_esta(): void
    {
        $this->post(route('inspection.store'), ['name' => 'Rodrigo', 'phone' => '+56987654321'])
            ->assertSessionHasErrors(['t_brand', 't_model', 't_year', 't_comuna']);

        $this->assertSame(0, Lead::count());
    }

    public function test_el_enlace_de_la_publicacion_es_opcional_pero_debe_ser_una_url(): void
    {
        $datos = $this->solicitudValida();
        unset($datos['t_listing_url']);

        $this->post(route('inspection.store'), $datos)->assertSessionHasNoErrors();

        $this->post(route('inspection.store'), $this->solicitudValida(['t_listing_url' => 'chileautos punto cl']))
            ->assertSessionHasErrors('t_listing_url');

        $this->assertSame(1, Lead::count());
    }

    public function test_el_campo_trampa_bloquea_el_envio_automatizado(): void
    {
        $this->post(route('inspection.store'), $this->solicitudValida(['website' => 'https://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Lead::count());
    }

    /** El servicio tiene que estar visible desde la navegación, no solo por URL directa. */
    public function test_el_servicio_se_ofrece_desde_la_portada_y_desde_nosotros(): void
    {
        foreach ([route('home'), route('about')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee(route('inspection.create'), escape: false);
        }
    }
}
