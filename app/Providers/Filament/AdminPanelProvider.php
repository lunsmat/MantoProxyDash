<?php

namespace App\Providers\Filament;

use Filament\Auth\Pages\EditProfile;
use Filament\Enums\ThemeMode;
use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
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
            ->path('/')
            ->login()
            ->colors([
                'primary' => [
                    50  => 'oklch(0.97 0.014 264.1)',
                    100 => 'oklch(0.932 0.032 264.1)',
                    200 => 'oklch(0.882 0.059 264.1)',
                    300 => 'oklch(0.809 0.105 264.1)',
                    400 => 'oklch(0.707 0.165 264.1)',
                    500 => 'oklch(0.623 0.201 264.1)', // cor original
                    600 => 'oklch(0.546 0.235 264.1)',
                    700 => 'oklch(0.488 0.243 264.1)',
                    800 => 'oklch(0.424 0.199 264.1)',
                    900 => 'oklch(0.379 0.146 264.1)',
                    950 => 'oklch(0.282 0.091 264.1)',

                ],
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ])->defaultThemeMode(ThemeMode::Light)
            ->brandName('MantoProxy')
            ->profile(EditProfile::class);
    }
}
