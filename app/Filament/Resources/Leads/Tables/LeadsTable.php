<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Lead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Recibido')
                    ->since()
                    ->tooltip(fn (Lead $record) => $record->created_at?->format('d/m/Y H:i'))
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (LeadType $state) => match ($state) {
                        LeadType::Tasacion => 'warning',
                        LeadType::Revision => 'success',
                        LeadType::Consulta => 'info',
                    })
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
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

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(LeadType::class),

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(LeadStatus::class)
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Sin leads todavía');
    }
}
