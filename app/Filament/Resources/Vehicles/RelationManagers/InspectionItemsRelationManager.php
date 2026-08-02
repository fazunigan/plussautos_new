<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Enums\InspectionCategory;
use App\Enums\InspectionStatus;
use App\Support\InspectionChecklist;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InspectionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'inspectionItems';

    protected static ?string $title = 'Hoja de inspección';

    protected static ?string $modelLabel = 'punto';

    protected static ?string $pluralModelLabel = 'puntos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->label('Categoría')
                    ->options(InspectionCategory::class)
                    ->required(),

                TextInput::make('label')
                    ->label('Punto revisado')
                    ->required()
                    ->maxLength(255),

                Select::make('status')
                    ->label('Estado')
                    ->options(InspectionStatus::class)
                    ->default(InspectionStatus::Ok->value)
                    ->required()
                    ->live(),

                Textarea::make('note')
                    ->label('Qué encontramos')
                    ->rows(3)
                    ->maxLength(500)
                    ->helperText('Específico y concreto: "Raya de 8 cm en la puerta trasera derecha" sirve; "detalle menor" no.')
                    ->visible(fn (Get $get) => $get('status') !== InspectionStatus::Ok->value)
                    ->required(fn (Get $get) => $get('status') !== InspectionStatus::Ok->value),

                SpatieMediaLibraryFileUpload::make('evidence')
                    ->label('Foto del detalle')
                    ->collection('evidence')
                    ->image()
                    ->imageEditor()
                    ->helperText('Foto de cerca y sin retoque. Una foto retocada destruye el sentido del sitio.')
                    ->visible(fn (Get $get) => $get('status') !== InspectionStatus::Ok->value),

                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Punto revisado')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                TextColumn::make('note')
                    ->label('Observación')
                    ->limit(60)
                    ->wrap()
                    ->toggleable(),

                SpatieMediaLibraryImageColumn::make('evidence')
                    ->label('Foto')
                    ->collection('evidence')
                    ->conversion('thumb')
                    ->imageHeight(36),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(InspectionCategory::class),

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(InspectionStatus::class),
            ])
            ->headerActions([
                // Cargar 33 puntos a mano desde el celular no es viable.
                Action::make('cargarPauta')
                    ->label('Cargar pauta estándar')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->visible(fn () => $this->getOwnerRecord()->inspectionItems()->doesntExist())
                    ->action(function (): void {
                        $vehicle = $this->getOwnerRecord();

                        foreach (InspectionChecklist::template() as $item) {
                            $vehicle->inspectionItems()->create($item);
                        }

                        Notification::make()
                            ->title('Pauta cargada')
                            ->body(InspectionChecklist::totalPoints().' puntos listos para revisar.')
                            ->success()
                            ->send();
                    }),

                CreateAction::make()->label('Agregar punto'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Sin hoja de inspección')
            ->emptyStateDescription('Carga la pauta estándar y revisa punto por punto.');
    }
}
