<?php

namespace Database\Seeders;

use App\Enums\BodyType;
use App\Enums\Fuel;
use App\Enums\InspectionStatus;
use App\Enums\Traction;
use App\Enums\Transmission;
use App\Enums\VehicleOrigin;
use App\Enums\VehicleStatus;
use App\Models\Brand;
use App\Models\InspectionItem;
use App\Models\Lead;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Support\InspectionChecklist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * No usa WithoutModelEvents a propósito: los slugs de los vehículos se
 * generan en los eventos del modelo y sin ellos el catálogo quedaría sin URLs.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Observaciones realistas por categoría. La gracia del sitio es que estos
     * textos sean específicos, así que los datos de prueba también lo son.
     *
     * @var array<string, list<string>>
     */
    private const OBSERVATIONS = [
        'carroceria' => [
            'Raya de 8 cm en la puerta trasera derecha, no llega a la lámina',
            'Abolladura leve en el parachoques trasero, lado izquierdo',
            'Diferencia de tono en el tapabarro izquierdo por repintado anterior',
            'Picadura de piedra en el parabrisas, fuera del campo de visión',
        ],
        'neumaticos' => [
            'Neumáticos delanteros al 40% de dibujo',
            'Neumático de repuesto sin uso pero con más de 6 años',
        ],
        'frenos' => [
            'Pastillas delanteras al 35%, conviene cambiarlas en unos 8.000 km',
            'Discos delanteros con rebaje leve, dentro de tolerancia',
        ],
        'interior' => [
            'Desgaste en el tapiz del asiento del conductor',
            'Rayas superficiales en la pantalla multimedia',
            'Botón del alzavidrios trasero derecho con tacto duro',
        ],
        'motor' => [
            'Filtración menor por tapa de válvulas, sellada en la última mantención',
        ],
        'suspension' => [
            'Ruido leve del amortiguador delantero izquierdo al pasar lomos de toro',
        ],
        'transmision' => [
            'Segundo cambio levemente duro en frío, normaliza al calentar',
        ],
        'documentacion' => [
            'Permiso de circulación vence en marzo',
        ],
    ];

    /** @var array<string, list<string>> */
    private const CATALOG = [
        'Toyota' => ['RAV4', 'Corolla', 'Hilux', 'Yaris', 'Land Cruiser Prado'],
        'Mazda' => ['CX-5', 'CX-3', 'Mazda 3', 'BT-50'],
        'Hyundai' => ['Tucson', 'Santa Fe', 'Accent', 'Creta'],
        'Kia' => ['Sportage', 'Rio', 'Seltos'],
        'Chevrolet' => ['Sail', 'Tracker', 'Groove'],
        'Nissan' => ['Qashqai', 'Versa', 'Navara'],
        'Suzuki' => ['Swift', 'Vitara', 'S-Cross'],
        'Ford' => ['Ranger', 'Escape'],
        'Peugeot' => ['2008', '208'],
        'Subaru' => ['XV', 'Forester'],
    ];

    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@plussautos.cl'],
            [
                'name' => 'Administración Pluss Autos',
                'password' => 'password',
                'is_admin' => true,
            ],
        );

        SiteSetting::query()->firstOrCreate([], [
            'whatsapp' => '+56 9 0000 0000',
            'phone' => '+56 9 0000 0000',
            'email' => 'contacto@plussautos.cl',
            'instagram' => 'https://instagram.com/plussautos',
            'about_intro' => 'Compramos, revisamos y vendemos autos usados. Publicamos la inspección completa de cada uno, con los defectos incluidos.',
        ]);

        $models = $this->seedCatalog();

        foreach ($this->vehicleData() as $index => $data) {
            $this->createVehicle($models, $data, $index);
        }

        Lead::factory()->count(4)->create();
        Lead::factory()->appraisal()->count(6)->create();
    }

    /** @return array<string, VehicleModel> */
    private function seedCatalog(): array
    {
        $models = [];

        foreach (self::CATALOG as $brandName => $modelNames) {
            $brand = Brand::query()->firstOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName],
            );

            foreach ($modelNames as $modelName) {
                $models["{$brandName}|{$modelName}"] = VehicleModel::query()->firstOrCreate(
                    ['brand_id' => $brand->id, 'slug' => Str::slug($modelName)],
                    ['name' => $modelName],
                );
            }
        }

        return $models;
    }

    /**
     * @return list<array{0:string,1:string,2:?string,3:int,4:int,5:int,6:Transmission,7:Fuel,8:BodyType,9:int,10:int,11:Traction,12:string,13:int}>
     */
    private function vehicleData(): array
    {
        return [
            ['Mazda', 'CX-5', 'GT AWD 2.5', 2019, 16_990_000, 78_400, Transmission::Automatica, Fuel::Bencina, BodyType::Suv, 2500, 5, Traction::Awd, 'Gris', 2],
            ['Toyota', 'RAV4', 'Limited 2.5 4x4', 2021, 24_500_000, 46_200, Transmission::Automatica, Fuel::Hibrido, BodyType::Suv, 2500, 5, Traction::FourByFour, 'Blanco', 1],
            ['Toyota', 'Corolla', 'XEI 1.8', 2020, 13_800_000, 62_100, Transmission::Cvt, Fuel::Bencina, BodyType::Sedan, 1800, 4, Traction::FourByTwo, 'Plata', 1],
            ['Hyundai', 'Tucson', 'GLS 2.0', 2018, 12_400_000, 96_800, Transmission::Automatica, Fuel::Bencina, BodyType::Suv, 2000, 5, Traction::FourByTwo, 'Negro', 2],
            ['Kia', 'Sportage', 'LX 2.0 Diésel', 2019, 15_200_000, 88_300, Transmission::Automatica, Fuel::Diesel, BodyType::Suv, 2000, 5, Traction::FourByFour, 'Blanco', 1],
            ['Toyota', 'Hilux', 'SRV 2.8 4x4', 2020, 26_900_000, 104_500, Transmission::Automatica, Fuel::Diesel, BodyType::Camioneta, 2800, 4, Traction::FourByFour, 'Gris', 1],
            ['Chevrolet', 'Sail', 'LT 1.5', 2019, 6_500_000, 71_200, Transmission::Manual, Fuel::Bencina, BodyType::Sedan, 1500, 4, Traction::FourByTwo, 'Rojo', 2],
            ['Suzuki', 'Swift', 'GL 1.2', 2021, 8_900_000, 34_700, Transmission::Manual, Fuel::Bencina, BodyType::Hatchback, 1200, 5, Traction::FourByTwo, 'Azul', 1],
            ['Nissan', 'Qashqai', 'Advance 2.0', 2018, 11_700_000, 108_900, Transmission::Cvt, Fuel::Bencina, BodyType::Suv, 2000, 5, Traction::FourByTwo, 'Gris', 3],
            ['Mazda', 'Mazda 3', 'V 2.0 Sport', 2022, 17_400_000, 28_600, Transmission::Automatica, Fuel::Bencina, BodyType::Hatchback, 2000, 5, Traction::FourByTwo, 'Rojo', 1],
            ['Ford', 'Ranger', 'XLT 3.2 4x4', 2019, 21_500_000, 121_400, Transmission::Automatica, Fuel::Diesel, BodyType::Camioneta, 3200, 4, Traction::FourByFour, 'Blanco', 2],
            ['Peugeot', '2008', 'Allure 1.6', 2020, 11_200_000, 55_300, Transmission::Automatica, Fuel::Bencina, BodyType::Suv, 1600, 5, Traction::FourByTwo, 'Blanco', 1],
            ['Subaru', 'Forester', 'AWD 2.0', 2017, 12_900_000, 132_700, Transmission::Cvt, Fuel::Bencina, BodyType::Suv, 2000, 5, Traction::Awd, 'Verde', 2],
            ['Hyundai', 'Accent', 'GL 1.4', 2021, 9_600_000, 41_800, Transmission::Manual, Fuel::Bencina, BodyType::Sedan, 1400, 4, Traction::FourByTwo, 'Plata', 1],
        ];
    }

    /** @param  array<string, VehicleModel>  $models */
    private function createVehicle(array $models, array $data, int $index): void
    {
        [$brandName, $modelName, $version, $year, $price, $km, $transmission, $fuel, $bodyType, $cc, $doors, $traction, $color, $owners] = $data;

        $model = $models["{$brandName}|{$modelName}"];
        $isConsignment = $index % 3 === 0;

        $vehicle = Vehicle::create([
            'brand_id' => $model->brand_id,
            'vehicle_model_id' => $model->id,
            'version' => $version,
            'year' => $year,
            'price' => $price,
            'mileage_km' => $km,
            'transmission' => $transmission,
            'fuel' => $fuel,
            'body_type' => $bodyType,
            'engine_cc' => $cc,
            'doors' => $doors,
            'traction' => $traction,
            'color' => $color,
            'owners_count' => $owners,
            'description' => "{$brandName} {$modelName} {$year} con {$owners} ".Str::plural('dueño', $owners)
                .'. Mantenciones al día y revisión completa publicada en esta ficha.',
            'status' => match (true) {
                $index === 3 => VehicleStatus::Reserved,
                $index === 12 => VehicleStatus::Sold,
                default => VehicleStatus::Available,
            },
            'published_at' => now()->subDays(($index * 6) + 3),
            'sold_at' => $index === 12 ? now()->subDays(4) : null,
            'featured' => $index < 2,
            'origin' => $isConsignment ? VehicleOrigin::Consignment : VehicleOrigin::Own,
            'plate' => strtoupper(fake()->bothify('??##??')),
            'consignor_name' => $isConsignment ? fake()->name() : null,
            'consignor_phone' => $isConsignment ? '+569'.fake()->numerify('########') : null,
            'purchase_price' => $isConsignment ? null : (int) ($price * 0.87),
            'commission_amount' => $isConsignment ? 450_000 : null,
            'location' => $isConsignment ? 'Domicilio del consignante, Ñuñoa' : 'Bodega, Quilicura',
            'internal_notes' => $isConsignment ? 'Mandato firmado. Coordinar visitas con el dueño.' : null,
        ]);

        $this->createInspection($vehicle, $index);
    }

    private function createInspection(Vehicle $vehicle, int $index): void
    {
        // Cada auto tiene entre 2 y 5 observaciones. Un auto usado sin ninguna
        // observación no sería creíble, que es justamente el punto del sitio.
        $observationCount = 2 + ($index % 4);
        $chosen = collect(self::OBSERVATIONS)
            ->flatMap(fn (array $notes, string $category) => array_map(
                fn (string $note) => ['category' => $category, 'note' => $note],
                $notes,
            ))
            ->shuffle()
            ->take($observationCount)
            ->keyBy('note');

        // Array plano, no Collection: el decremento de más abajo necesita
        // modificación por referencia, que ArrayAccess no entrega.
        $usedCategories = $chosen->pluck('category')->countBy()->all();

        foreach (InspectionChecklist::template() as $item) {
            $status = InspectionStatus::Ok;
            $note = null;

            if (($usedCategories[$item['category']] ?? 0) > 0) {
                $match = $chosen->firstWhere('category', $item['category']);

                if ($match !== null) {
                    $status = $index % 5 === 0 ? InspectionStatus::Reparado : InspectionStatus::Observacion;
                    $note = $match['note'];
                    $chosen->forget($match['note']);
                    $usedCategories[$item['category']]--;
                }
            }

            InspectionItem::create([
                'vehicle_id' => $vehicle->id,
                'category' => $item['category'],
                'label' => $item['label'],
                'status' => $status,
                'note' => $note,
                'sort_order' => $item['sort_order'],
            ]);
        }
    }
}
