<?php

namespace App\Support;

use App\Enums\InspectionCategory;
use App\Enums\InspectionStatus;

/**
 * Pauta estándar de revisión. Es la misma para todos los autos a propósito:
 * si la lista cambiara según el auto, el comprador no podría comparar dos
 * fichas entre sí, que es justamente lo que le sirve.
 */
final class InspectionChecklist
{
    /** @var array<string, list<string>> */
    private const POINTS = [
        'motor' => [
            'Nivel y estado del aceite',
            'Refrigerante y sistema de enfriamiento',
            'Correas y mangueras',
            'Partida en frío',
            'Fugas visibles',
        ],
        'transmision' => [
            'Cambios y acoplamiento',
            'Estado del embrague',
            'Fugas de caja',
        ],
        'frenos' => [
            'Pastillas y discos delanteros',
            'Pastillas y discos traseros',
            'Líquido de frenos',
            'Freno de mano',
        ],
        'suspension' => [
            'Amortiguadores delanteros',
            'Amortiguadores traseros',
            'Bujes y terminales',
            'Ruidos en marcha',
        ],
        'neumaticos' => [
            'Profundidad de dibujo',
            'Desgaste parejo',
            'Neumático de repuesto',
        ],
        'carroceria' => [
            'Pintura y diferencias de tono',
            'Golpes y abolladuras',
            'Parabrisas y vidrios',
            'Bajos y corrosión',
            'Luces exteriores',
        ],
        'interior' => [
            'Tapices y asientos',
            'Tablero y testigos',
            'Aire acondicionado',
            'Multimedia y parlantes',
            'Alzavidrios y cierre centralizado',
        ],
        'documentacion' => [
            'Permiso de circulación al día',
            'Revisión técnica vigente',
            'Sin multas ni prendas',
            'Historial de mantenciones',
        ],
    ];

    /**
     * Pauta completa lista para crear los ítems de un vehículo nuevo.
     *
     * @return list<array{category: string, label: string, status: string, sort_order: int}>
     */
    public static function template(): array
    {
        $items = [];
        $order = 0;

        foreach (self::POINTS as $category => $labels) {
            foreach ($labels as $label) {
                $items[] = [
                    'category' => $category,
                    'label' => $label,
                    'status' => InspectionStatus::Ok->value,
                    'sort_order' => $order++,
                ];
            }
        }

        return $items;
    }

    /** @return list<string> */
    public static function pointsFor(InspectionCategory $category): array
    {
        return self::POINTS[$category->value] ?? [];
    }

    public static function totalPoints(): int
    {
        return array_sum(array_map('count', self::POINTS));
    }
}
