<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Widgets\StatsOverview;
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

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('/')
            ->login()
            ->passwordReset()
            ->databaseNotifications()
            ->colors([
                'primary' => [
                    50 => '250, 245, 255',
                    100 => '243, 232, 255',
                    200 => '233, 213, 255',
                    300 => '216, 180, 254',
                    400 => '192, 132, 252',
                    500 => '168, 85, 247',
                    600 => '147, 51, 234',
                    700 => '126, 34, 206',
                    800 => '107, 33, 168',
                    900 => '88, 28, 135',
                    950 => '59, 7, 100',
                ],
                'gray' => Color::Gray,
            ])
            //Custom configs
            // ->spa()
            ->unsavedChangesAlerts()
            ->brandName('Coupons')
            // ->topNavigation()
            //
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->widgets([
            //     Widgets\AccountWidget::class,
            //     StatsOverview::class,
            // ])
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
