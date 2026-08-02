<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Login del panel.
 *
 * Extiende el de Filament en vez de reemplazar su vista: la presentación se
 * resuelve por CSS desde el proveedor del panel, así que una actualización de
 * Filament no rompe la pantalla de acceso.
 */
class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Panel de administración';
    }

    public function getSubheading(): ?string
    {
        return 'Ingresa para gestionar el catálogo y los leads.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('email')
                ->label('Correo')
                ->email()
                ->required()
                ->autocomplete('username')
                ->autofocus()
                ->extraInputAttributes(['tabindex' => 1]),

            TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->revealable()
                ->required()
                ->extraInputAttributes(['tabindex' => 2]),

            Checkbox::make('remember')
                ->label('Mantener la sesión iniciada'),
        ]);
    }

    /** @return array<string, string> */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }
}
