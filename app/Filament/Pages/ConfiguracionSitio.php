<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Los datos de contacto viven en base de datos y no en el .env para que se
 * puedan cambiar sin desplegar: cambiar el número de WhatsApp no debería
 * requerir un despliegue.
 */
class ConfiguracionSitio extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.configuracion-sitio';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configuración';

    protected static ?string $title = 'Configuración del sitio';

    protected static ?int $navigationSort = 9;

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Contacto')
                    ->description('Aparecen en el encabezado, el pie de página y los botones de WhatsApp.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->placeholder('+56 9 1234 5678')
                            ->required()
                            ->helperText('Se limpia automáticamente para armar el enlace de wa.me.'),

                        TextInput::make('phone')->label('Teléfono')->tel(),
                        TextInput::make('email')->label('Correo')->email(),
                    ]),

                Section::make('Redes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('instagram')->label('Instagram')->url()->placeholder('https://instagram.com/...'),
                        TextInput::make('facebook')->label('Facebook')->url()->placeholder('https://facebook.com/...'),
                    ]),

                Section::make('Punto de atención')
                    ->description('Opcional. Mientras estén vacíos, la página de contacto simplemente no muestra estas filas.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address')
                            ->label('Dirección')
                            ->placeholder('Av. Siempre Viva 742, Ñuñoa'),
                        TextInput::make('hours')
                            ->label('Horario')
                            ->placeholder('Lunes a viernes de 10 a 19 h'),
                    ]),

                Section::make('Textos')
                    ->schema([
                        Textarea::make('about_intro')
                            ->label('Presentación')
                            ->rows(3)
                            ->maxLength(600),

                        Textarea::make('about_process')
                            ->label('Cómo trabajamos')
                            ->rows(5)
                            ->maxLength(1500),
                    ]),
            ]);
    }

    public function save(): void
    {
        SiteSetting::current()->update($this->form->getState());

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }
}
