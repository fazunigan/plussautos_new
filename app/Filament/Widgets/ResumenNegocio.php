<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Enums\VehicleStatus;
use App\Models\Lead;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Las cuatro cifras por las que se maneja una compraventa. Reemplazan a los
 * widgets que trae Filament de fábrica, que no dicen nada del negocio.
 */
class ResumenNegocio extends StatsOverviewWidget
{
    protected ?string $heading = 'Cómo va el mes';

    protected static ?int $sort = 1;

    /**
     * Sin carga diferida: son cuatro conteos baratos y es lo primero que se
     * mira al entrar. Cargarlos aparte solo agrega un parpadeo de esqueleto.
     */
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $publicados = Vehicle::query()->listable()->count();
        $reservados = Vehicle::query()->where('status', VehicleStatus::Reserved->value)->count();
        $borradores = Vehicle::query()->where('status', VehicleStatus::Draft->value)->count();

        $vendidosMes = Vehicle::query()
            ->where('status', VehicleStatus::Sold->value)
            ->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $leadsMes = Lead::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $sinContactar = Lead::query()->where('status', LeadStatus::Nuevo->value)->count();

        return [
            Stat::make('Autos publicados', (string) $publicados)
                ->description($reservados > 0
                    ? "{$reservados} ".str('reservado')->plural($reservados)
                    : ($borradores > 0 ? "{$borradores} en borrador" : 'Todos disponibles'))
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Días en stock promedio', $this->promedioDiasEnStock())
                ->description('De los autos publicados')
                ->descriptionIcon('heroicon-m-clock')
                ->color($this->colorDias()),

            Stat::make('Leads del mes', (string) $leadsMes)
                ->description($sinContactar > 0
                    ? "{$sinContactar} sin contactar"
                    : 'Todos contactados')
                ->descriptionIcon('heroicon-m-inbox')
                ->color($sinContactar > 0 ? 'warning' : 'success'),

            Stat::make('Vendidos este mes', (string) $vendidosMes)
                ->description(now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }

    private function promedioDiasEnStock(): string
    {
        $publicados = Vehicle::query()->listable()->get(['published_at', 'sold_at']);

        if ($publicados->isEmpty()) {
            return '—';
        }

        $promedio = $publicados
            ->map(fn (Vehicle $v) => (int) $v->published_at->diffInDays(now()))
            ->avg();

        return round($promedio).' días';
    }

    private function colorDias(): string
    {
        $publicados = Vehicle::query()->listable()->get(['published_at', 'sold_at']);

        if ($publicados->isEmpty()) {
            return 'gray';
        }

        $promedio = $publicados->map(fn (Vehicle $v) => (int) $v->published_at->diffInDays(now()))->avg();

        return match (true) {
            $promedio > 90 => 'danger',
            $promedio > 45 => 'warning',
            default => 'success',
        };
    }
}
