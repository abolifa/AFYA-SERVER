<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\CentersMap;
use App\Filament\Widgets\LatestPendingAppointments;
use App\Filament\Widgets\LatestStockMovement;
use App\Filament\Widgets\LowStockProduct;
use App\Filament\Widgets\SystemStats;
use App\Filament\Widgets\TopCentersConsumption;
use App\Filament\Widgets\TopConsumedProduct;
use App\Filament\Widgets\TopDevices;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->font('Cairo')
            ->maxContentWidth('full')
            ->colors([
                'primary' => Color::Teal,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                SystemStats::class,
                LatestPendingAppointments::class,
                TopCentersConsumption::class,
                TopConsumedProduct::class,
                TopDevices::class,
                LowStockProduct::class,
                LatestStockMovement::class,
                CentersMap::class,
            ])
            ->middleware([
                SetTheme::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
                ThemesPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
