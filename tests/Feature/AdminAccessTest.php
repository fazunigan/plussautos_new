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
}
