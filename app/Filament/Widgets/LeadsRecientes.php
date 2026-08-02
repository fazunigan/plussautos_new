<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/** Lo primero que hay que mirar al entrar: quién escribió y no ha sido contactado. */
class LeadsRecientes extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Leads sin contactar')
            ->description('Los más antiguos primero: son los que llevan más tiempo esperando.')
            ->query(
                Lead::query()
                    ->where('status', LeadStatus::Nuevo->value)
                    ->with('vehicle')
                    ->orderBy('created_at')
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Espera')
                    ->since()
                    ->description(fn (Lead $record) => $record->created_at?->format('d/m/Y H:i')),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (LeadType $state) => match ($state) {
                        LeadType::Tasacion => 'warning',
                        LeadType::Revision => 'success',
                        LeadType::Consulta => 'info',
                    }),

                TextColumn::make('name')
                    ->label('Nombre'),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->copyable()
                    ->copyMessage('Teléfono copiado'),

                TextColumn::make('about')
                    ->label('Sobre qué')
                    ->state(fn (Lead $record) => $record->type->aboutExternalVehicle()
                        ? $record->appraisalSummary()
                        : $record->vehicle?->fullTitle())
                    ->description(fn (Lead $record) => $record->appraisalDetail())
                    ->placeholder('Consulta general')
                    ->wrap(),
            ])
            ->recordActions([
                Action::make('abrir')
                    ->label('Abrir')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (Lead $record) => LeadResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('No hay leads esperando')
            ->emptyStateDescription('Todos los que escribieron ya fueron contactados.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
