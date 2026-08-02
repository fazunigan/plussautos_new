<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visitante_no_entra_al_panel(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    public function test_un_usuario_sin_permiso_de_admin_no_entra_al_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_el_administrador_entra_al_panel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin')->assertSuccessful();
    }

    public function test_el_administrador_ve_el_listado_de_vehiculos(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/vehicles')->assertSuccessful();
    }

    public function test_el_administrador_ve_la_bandeja_de_leads(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/leads')->assertSuccessful();
    }

    public function test_el_acceso_esta_en_espanol_y_con_la_marca_del_negocio(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Panel de administración')
            ->assertSee('Ingresa para gestionar el catálogo y los leads.')
            ->assertSee('Mantener la sesión iniciada')
            // Logo a color: en el layout simple va dentro de la tarjeta blanca.
            ->assertSee('img/logo.webp', escape: false);
    }

    /**
     * El panel es del negocio: nada de widgets de fábrica ni de marca ajena.
     *
     * Se buscan rastros visibles, no la palabra "filament" a secas: las rutas
     * de los assets y el objeto JavaScript la contienen y son plumbing interno,
     * no marca a la vista del usuario.
     */
    public function test_el_panel_no_muestra_marca_ajena(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $contenido = $this->actingAs($admin)->get('/admin')->assertOk()->getContent();

        foreach ([
            'filamentphp.com',
            'Powered by',
            'Documentation',
            'Github',
        ] as $rastro) {
            $this->assertStringNotContainsString(
                $rastro,
                $contenido,
                "El panel muestra «{$rastro}».",
            );
        }

        $this->assertStringContainsString('Pluss Autos', $contenido);
    }

    public function test_el_inicio_del_panel_muestra_las_metricas_del_negocio(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('Autos publicados')
            ->assertSee('Días en stock promedio')
            ->assertSee('Leads del mes')
            ->assertSee('Vendidos este mes');
    }
}
