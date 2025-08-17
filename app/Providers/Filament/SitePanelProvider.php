<?php

namespace App\Providers\Filament;

use App\Filament\Site\Widgets\BlogStats;
use App\Filament\Site\Widgets\Complaints;
use App\Filament\Site\Widgets\SiteStats;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SitePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('site')
            ->path('site')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->font('Cairo')
            ->discoverResources(in: app_path('Filament/Site/Resources'), for: 'App\\Filament\\Site\\Resources')
            ->discoverPages(in: app_path('Filament/Site/Pages'), for: 'App\\Filament\\Site\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                SiteStats::class,
                BlogStats::class,
                Complaints::class,
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
