<?php

namespace App\Filament\Resources\Vehicles\Tables;

use App\Enums\BodyType;
use App\Enums\VehicleOrigin;
use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('gallery')
                    ->label('')
                    ->collection('gallery')
                    ->conversion('thumb')
                    ->limit(1)
                    ->imageHeight(44),

                TextColumn::make('brand.name')
                    ->label('Auto')
                    ->description(fn (Vehicle $record) => trim("{$record->vehicleModel?->name} {$record->version}"))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Precio')
                    ->formatStateUsing(fn (int $state) => '$'.number_format($state, 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('mileage_km')
                    ->label('Km')
                    ->formatStateUsing(fn (int $state) => number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                TextColumn::make('origin')
                    ->label('Origen')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                // La métrica por la que se maneja una compraventa.
                TextColumn::make('days_in_stock')
                    ->label('Días en stock')
                    ->state(fn (Vehicle $record) => $record->daysInStock())
                    ->placeholder('Sin publicar')
                    ->badge()
                    ->color(fn (?int $state) => match (true) {
                        $state === null => 'gray',
                        $state > 90 => 'danger',
                        $state > 45 => 'warning',
                        default => 'success',
                    })
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('published_at', $direction === 'asc' ? 'desc' : 'asc')),

                TextColumn::make('inspection_items_count')
                    ->label('Detalles')
                    ->counts([
                        'inspectionItems' => fn ($query) => $query->details(),
                    ])
                    ->toggleable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(VehicleStatus::class)
                    ->multiple(),

                SelectFilter::make('origin')
                    ->label('Origen')
                    ->options(VehicleOrigin::class),

                SelectFilter::make('body_type')
                    ->label('Carrocería')
                    ->options(BodyType::class),

                SelectFilter::make('brand')
                    ->label('Marca')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
