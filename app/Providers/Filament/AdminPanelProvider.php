<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Filament\Widgets\GeneralStatsWidget;
use App\Filament\Widgets\OrdersCountWidget;
use App\Filament\Widgets\LatestOrdersStats;
use App\Filament\Widgets\WarehouseInventoryChart;
use App\Filament\Widgets\WarehouseStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      ->default()
      ->id('admin')
      ->path('admin')
      ->navigationGroups([
        'إدارة المنتجات',
        'إدارة المستخدمين',
        'شحن واستيراد',
      ])
      ->login()
      ->brandName('المنزل الحديث')
      ->brandLogo(asset('images/home-logo-black_dicco2.svg'))
      ->darkModeBrandLogo(asset('images/home-logo-white_c2et5l.svg'))
      ->brandLogoHeight('3rem')
      ->broadcasting()
      ->colors([
        'primary' => '#025043',
      ])
      ->databaseNotifications()
      ->databaseNotificationsPolling('30s')
      ->font('Cairo')
      //   ->renderHook(
      //     \Filament\View\PanelsRenderHook::HEAD_END,
      //     fn(): string => Blade::render('
      //     @vite([\'resources/js/app.js\'])

      //     <link rel="manifest" href="/build/manifest.webmanifest">

      //     <link rel="apple-touch-icon" href="/logo-192.png">

      //     <meta name="theme-color" content="#0d6efd">
      //     <meta name="apple-mobile-web-app-capable" content="yes">
      //     <meta name="apple-mobile-web-app-status-bar-style" content="default">

      //     <style>
      //         html, body { font-size: 1.05rem !important; }
      //         .fi-main { font-size: 1.05rem !important; }
      //     </style>
      // '),

      ->renderHook(
        \Filament\View\PanelsRenderHook::HEAD_END,
        fn(): string => Blade::render('
        @vite([\'resources/js/app.js\', \'resources/js/filament-fcm.js\'])

        <link rel="manifest" href="/build/manifest.webmanifest">
        <link rel="apple-touch-icon" href="/logo-192.png">

        <meta name="theme-color" content="#0d6efd">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">

        <style>
            html, body { font-size: 1.05rem !important; }
            .fi-main { font-size: 1.05rem !important; }
        </style>
    '),
      )
      ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
      ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
      ->pages([
        Pages\Dashboard::class,
      ])
      ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
      ->widgets([

        LatestOrdersStats::class,
        WarehouseStatsWidget::class,
        WarehouseInventoryChart::class,
        GeneralStatsWidget::class,
        OrdersCountWidget::class,
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
      ->plugins([
        FilamentShieldPlugin::make(),
      ])
      ->authMiddleware([
        Authenticate::class,
      ]);
  }
}
