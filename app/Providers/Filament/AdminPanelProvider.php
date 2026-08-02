<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Widgets\LeadsRecientes;
use App\Filament\Widgets\ResumenNegocio;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)

            // Marca del negocio, no la de Filament.
            ->brandName('Pluss Autos')
            // Logo a color: en el layout simple va dentro de la tarjeta blanca,
            // donde la versión monocromática blanca sería invisible.
            ->brandLogo(fn () => asset('img/logo.webp'))
            ->brandLogoHeight('2.5rem')
            ->favicon(fn () => asset('favicon.ico'))

            // Rampa explícita en vez de Color::hex(): la generada automáticamente
            // no dejaba #004AAD en el tono 600, que es el que Filament usa para
            // los botones, y salían lavados.
            ->colors([
                'primary' => [
                    50 => '#F0F5FD',
                    100 => '#E0ECFF',
                    200 => '#C1D9FE',
                    300 => '#94BCFB',
                    400 => '#5791ED',
                    500 => '#266AD2',
                    600 => '#004AAD', // el azul del logo
                    700 => '#003C90',
                    800 => '#002E71',
                    900 => '#012357',
                    950 => '#021435',
                ],
            ])

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.auth.estilos')->render(),
                scopes: Login::class,
            )

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])

            // Los widgets de fábrica (cuenta e información de Filament) no dicen
            // nada del negocio, así que no se registran.
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                ResumenNegocio::class,
                LeadsRecientes::class,
            ])

            ->navigationGroups([
                'Catálogo',
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
