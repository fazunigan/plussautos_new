<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Las aserciones van contra el slug del vehículo y no contra el nombre de la
 * marca: el nombre aparece también en el selector de filtros, así que buscarlo
 * en el HTML no distingue entre "está listado" y "está en el desplegable".
 */
class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private function vehicleFor(string $brandName, array $attributes = []): Vehicle
    {
        $brand = Brand::factory()->create([
            'name' => $brandName,
            'slug' => str($brandName)->slug(),
        ]);

        $model = VehicleModel::factory()->for($brand)->create();

        return Vehicle::factory()->create([
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            ...$attributes,
        ]);
    }

    public function test_el_catalogo_lista_los_vehiculos_publicados(): void
    {
        $mazda = $this->vehicleFor('Mazda');

        $this->get(route('vehicles.index'))
            ->assertOk()
            ->assertSee($mazda->slug);
    }

    public function test_los_borradores_no_aparecen_en_el_catalogo(): void
    {
        $publicado = $this->vehicleFor('Peugeot');
        $borrador = $this->vehicleFor('Ferrari', ['status' => 'draft', 'published_at' => null]);

        $this->get(route('vehicles.index'))
            ->assertOk()
            ->assertSee($publicado->slug)
            ->assertDontSee($borrador->slug);
    }

    public function test_los_vendidos_no_aparecen_en_el_catalogo(): void
    {
        $vendido = $this->vehicleFor('Subaru', ['status' => 'sold', 'sold_at' => now()]);

        $this->get(route('vehicles.index'))
            ->assertOk()
            ->assertDontSee($vendido->slug);
    }

    public function test_filtra_por_marca(): void
    {
        $mazda = $this->vehicleFor('Mazda');
        $toyota = $this->vehicleFor('Toyota');

        $this->get(route('vehicles.index', ['marca' => 'mazda']))
            ->assertOk()
            ->assertSee($mazda->slug)
            ->assertDontSee($toyota->slug);
    }

    public function test_filtra_por_rango_de_precio(): void
    {
        $barato = $this->vehicleFor('Kia', ['price' => 5_000_000]);
        $caro = $this->vehicleFor('Nissan', ['price' => 25_000_000]);

        $this->get(route('vehicles.index', ['precio_max' => 10_000_000]))
            ->assertOk()
            ->assertSee($barato->slug)
            ->assertDontSee($caro->slug);
    }

    public function test_filtra_por_kilometraje_maximo(): void
    {
        $poco = $this->vehicleFor('Suzuki', ['mileage_km' => 20_000]);
        $mucho = $this->vehicleFor('Chevrolet', ['mileage_km' => 190_000]);

        $this->get(route('vehicles.index', ['km_max' => 50_000]))
            ->assertOk()
            ->assertSee($poco->slug)
            ->assertDontSee($mucho->slug);
    }

    public function test_filtra_por_transmision(): void
    {
        $automatica = $this->vehicleFor('Honda', ['transmission' => 'automatica']);
        $mecanica = $this->vehicleFor('Fiat', ['transmission' => 'manual']);

        $this->get(route('vehicles.index', ['transmision' => 'automatica']))
            ->assertOk()
            ->assertSee($automatica->slug)
            ->assertDontSee($mecanica->slug);
    }

    public function test_ordena_por_precio_ascendente(): void
    {
        $caro = $this->vehicleFor('Alfa', ['price' => 30_000_000]);
        $barato = $this->vehicleFor('Beta', ['price' => 4_000_000]);

        $contenido = $this->get(route('vehicles.index', ['orden' => 'precio_asc']))
            ->assertOk()
            ->getContent();

        $this->assertLessThan(
            strpos($contenido, $caro->slug),
            strpos($contenido, $barato->slug),
            'El auto más barato debe aparecer antes que el más caro.',
        );
    }

    public function test_ordena_por_precio_descendente(): void
    {
        $caro = $this->vehicleFor('Alfa', ['price' => 30_000_000]);
        $barato = $this->vehicleFor('Beta', ['price' => 4_000_000]);

        $contenido = $this->get(route('vehicles.index', ['orden' => 'precio_desc']))
            ->assertOk()
            ->getContent();

        $this->assertLessThan(
            strpos($contenido, $barato->slug),
            strpos($contenido, $caro->slug),
        );
    }

    public function test_un_filtro_invalido_se_ignora_en_vez_de_romper(): void
    {
        $ford = $this->vehicleFor('Ford');

        $this->get(route('vehicles.index', [
            'transmision' => 'cohete',
            'anio_min' => 'ayer',
            'precio_max' => '-999',
            'orden' => 'lo-que-sea',
        ]))
            ->assertOk()
            ->assertSee($ford->slug);
    }

    public function test_los_filtros_viven_en_la_url_y_se_conservan_al_paginar(): void
    {
        $brand = Brand::factory()->create(['name' => 'Hyundai', 'slug' => 'hyundai']);
        $model = VehicleModel::factory()->for($brand)->create();
        Vehicle::factory()->count(15)->create([
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
        ]);

        $this->get(route('vehicles.index', ['marca' => 'hyundai']))
            ->assertOk()
            ->assertSee('marca=hyundai', escape: false);
    }

    public function test_el_catalogo_sin_resultados_ofrece_una_salida(): void
    {
        $this->vehicleFor('Mazda', ['price' => 20_000_000]);

        $this->get(route('vehicles.index', ['precio_max' => 1_000_000]))
            ->assertOk()
            ->assertSee('No hay autos con esos filtros');
    }
}
