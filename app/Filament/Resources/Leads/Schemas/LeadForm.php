<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\VehicleCondition;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quién escribió')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Tipo')
                            ->options(LeadType::class)
                            ->required()
                            ->live(),

                        TextInput::make('name')->label('Nombre')->required(),
                        TextInput::make('phone')->label('Teléfono')->tel()->required(),
                        TextInput::make('email')->label('Correo')->email(),

                        Textarea::make('message')
                            ->label('Mensaje')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Auto externo')
                    ->description('Datos del auto que el cliente quiere vender o que nos pide revisar.')
                    ->columns(2)
                    ->visible(fn (Get $get) => in_array($get('type'), [
                        LeadType::Tasacion->value,
                        LeadType::Revision->value,
                    ], true))
                    ->schema([
                        TextInput::make('t_brand')->label('Marca'),
                        TextInput::make('t_model')->label('Modelo'),
                        TextInput::make('t_version')->label('Versión'),
                        TextInput::make('t_year')->label('Año')->numeric(),
                        TextInput::make('t_mileage_km')->label('Kilometraje')->numeric()->suffix('km'),
                        Select::make('t_condition')
                            ->label('Estado declarado')
                            ->options(VehicleCondition::class),
                        TextInput::make('t_comuna')->label('Comuna'),
                        TextInput::make('t_plate')->label('Patente'),
                        TextInput::make('t_listing_url')
                            ->label('Enlace de la publicación')
                            ->url()
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('type') === LeadType::Revision->value),
                    ]),

                Section::make('Auto consultado')
                    ->visible(fn (Get $get) => $get('type') === LeadType::Consulta->value)
                    ->schema([
                        Select::make('vehicle_id')
                            ->label('Vehículo')
                            ->relationship('vehicle', 'slug')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Seguimiento')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options(LeadStatus::class)
                            ->default(LeadStatus::Nuevo->value)
                            ->required(),

                        TextInput::make('source')
                            ->label('Origen')
                            ->disabled()
                            ->dehydrated(false),

                        Textarea::make('internal_notes')
                            ->label('Notas internas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
