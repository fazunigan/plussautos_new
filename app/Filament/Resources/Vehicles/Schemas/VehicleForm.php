<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enums\BodyType;
use App\Enums\Fuel;
use App\Enums\Traction;
use App\Enums\Transmission;
use App\Enums\VehicleOrigin;
use App\Enums\VehicleStatus;
use App\Models\VehicleModel;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificación')
                    ->columns(2)
                    ->schema([
                        Select::make('brand_id')
                            ->label('Marca')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            // Cambiar la marca deja el modelo anterior sin sentido.
                            ->afterStateUpdated(fn (Set $set) => $set('vehicle_model_id', null))
                            ->createOptionForm([
                                TextInput::make('name')->label('Nombre')->required(),
                            ]),

                        Select::make('vehicle_model_id')
                            ->label('Modelo')
                            ->options(fn (Get $get) => $get('brand_id')
                                ? VehicleModel::query()
                                    ->where('brand_id', $get('brand_id'))
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                : [])
                            ->searchable()
                            ->required()
                            ->disabled(fn (Get $get) => ! $get('brand_id'))
                            ->helperText('Elige primero la marca.'),

                        TextInput::make('version')
                            ->label('Versión')
                            ->placeholder('GT AWD 2.5')
                            ->maxLength(255),

                        TextInput::make('year')
                            ->label('Año')
                            ->numeric()
                            ->minValue(1950)
                            ->maxValue((int) date('Y') + 1)
                            ->required(),
                    ]),

                Section::make('Especificaciones')
                    ->columns(2)
                    ->schema([
                        TextInput::make('mileage_km')
                            ->label('Kilometraje')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('km')
                            ->required(),

                        Select::make('transmission')
                            ->label('Transmisión')
                            ->options(Transmission::class)
                            ->required(),

                        Select::make('fuel')
                            ->label('Combustible')
                            ->options(Fuel::class)
                            ->required(),

                        Select::make('body_type')
                            ->label('Carrocería')
                            ->options(BodyType::class)
                            ->required(),

                        TextInput::make('engine_cc')
                            ->label('Cilindrada')
                            ->numeric()
                            ->suffix('cc'),

                        Select::make('traction')
                            ->label('Tracción')
                            ->options(Traction::class),

                        TextInput::make('doors')
                            ->label('Puertas')
                            ->numeric()
                            ->minValue(2)
                            ->maxValue(6),

                        TextInput::make('color')
                            ->label('Color')
                            ->maxLength(255),

                        TextInput::make('owners_count')
                            ->label('Dueños anteriores')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20),

                        TextInput::make('video_url')
                            ->label('Video de recorrido')
                            ->url()
                            ->helperText('URL para incrustar. El video reemplaza la visita al local, así que conviene que no falte.')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ]),

                Section::make('Fotos')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Galería')
                            ->collection('gallery')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->image()
                            ->imageEditor()
                            ->maxFiles(40)
                            ->helperText('La primera foto es la portada. Se pueden arrastrar para reordenar.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Precio y publicación')
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')
                            ->label('Precio')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->required(),

                        Select::make('status')
                            ->label('Estado')
                            ->options(VehicleStatus::class)
                            ->default(VehicleStatus::Draft->value)
                            ->required()
                            ->live(),

                        DateTimePicker::make('published_at')
                            ->label('Publicado el')
                            ->seconds(false)
                            ->helperText('Sin fecha de publicación el auto no aparece en el sitio.'),

                        DateTimePicker::make('sold_at')
                            ->label('Vendido el')
                            ->seconds(false)
                            ->visible(fn (Get $get) => $get('status') === VehicleStatus::Sold->value),

                        Toggle::make('featured')
                            ->label('Destacado en la portada'),
                    ]),

                Section::make('Interno')
                    ->description('Nada de esta sección se muestra en el sitio público.')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Select::make('origin')
                            ->label('Origen')
                            ->options(VehicleOrigin::class)
                            ->default(VehicleOrigin::Own->value)
                            ->required()
                            ->live(),

                        TextInput::make('plate')
                            ->label('Patente')
                            ->maxLength(10),

                        TextInput::make('purchase_price')
                            ->label('Precio de compra')
                            ->numeric()
                            ->prefix('$')
                            ->visible(fn (Get $get) => $get('origin') === VehicleOrigin::Own->value),

                        TextInput::make('consignor_name')
                            ->label('Consignante')
                            ->visible(fn (Get $get) => $get('origin') === VehicleOrigin::Consignment->value),

                        TextInput::make('consignor_phone')
                            ->label('Teléfono del consignante')
                            ->tel()
                            ->visible(fn (Get $get) => $get('origin') === VehicleOrigin::Consignment->value),

                        TextInput::make('commission_amount')
                            ->label('Comisión')
                            ->numeric()
                            ->prefix('$')
                            ->visible(fn (Get $get) => $get('origin') === VehicleOrigin::Consignment->value),

                        TextInput::make('location')
                            ->label('Dónde está el auto')
                            ->maxLength(255),

                        Textarea::make('internal_notes')
                            ->label('Notas internas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
