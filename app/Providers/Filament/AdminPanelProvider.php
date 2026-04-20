<?php

namespace App\Providers\Filament;

use App\Filament\Resources\CompanyTreasureResource\Widgets\CapitalStatsWidget;
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
use Filament\Navigation\NavigationGroup;


class AdminPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      ->default()
      ->id('admin')
      ->path('admin')
      ->sidebarCollapsibleOnDesktop()
      ->navigationGroups([
        NavigationGroup::make()
          ->label('إدارة المنتجات')
          ->icon('heroicon-o-shopping-bag'),

        NavigationGroup::make()
          ->label('إدارة الطلبات')
          ->icon('heroicon-o-clipboard-document-check'),

        NavigationGroup::make()
          ->label('نقاط البيع (POS)')
          ->icon('heroicon-o-computer-desktop'),

        NavigationGroup::make()
          ->label('إدارة المبيعات')
          ->icon('heroicon-o-presentation-chart-line'),

        NavigationGroup::make()
          ->label('شحن و استيراد')
          ->icon('heroicon-o-truck'),

        NavigationGroup::make()
          ->label('إدارة التوصيل')
          ->icon('heroicon-o-map-pin'),

        NavigationGroup::make()
          ->label('الإدارة المالية')
          ->icon('heroicon-o-banknotes'),

        NavigationGroup::make()
          ->label('التقارير والإحصائيات')
          ->icon('heroicon-o-chart-bar'),

        NavigationGroup::make()
          ->label('إدارة المستخدمين')
          ->icon('heroicon-o-users'),

        NavigationGroup::make()
          ->label('الإعدادات المتقدمة')
          ->icon('heroicon-o-cog-6-tooth'),
      ])
      ->login()
      ->brandName('وكالة نوح')
      ->brandLogo(asset('images/logo-no-black.png'))
      ->darkModeBrandLogo(asset('images/logo-no-white.svg'))
      ->favicon(asset('logo-no-black.png'))
      ->brandLogoHeight('5rem')
      ->broadcasting()
      ->colors([
        'primary' => '#025043',
      ])
      ->databaseNotifications()
      ->databaseNotificationsPolling('30s')
      ->font('Cairo')
      ->navigationItems([
        \Filament\Navigation\NavigationItem::make('شاشة الكاشير (POS)')
          ->group('إدارة المبيعات')
          ->icon('heroicon-o-computer-desktop')
          ->url(fn(): string => \App\Filament\Resources\CashierSaleResource\Pages\CashierPos::getUrl())
          ->sort(1)
          ->visible(fn(): bool => auth()->user()->hasAnyRole(['super_admin', 'sales_point_cashier'])),
      ])
      // ->navigationItems([
      //   \Filament\Navigation\NavigationItem::make('شاشة الكاشير (POS)')
      //     ->group('إدارة المبيعات')
      //     ->icon('heroicon-o-computer-desktop')
      //     ->url(fn(): string => CashierPos::getUrl())
      //     ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.cashier-pos'))
      //     ->sort(1)
      //     ->visible(fn(): bool => auth()->user()->hasAnyRole(['super_admin', 'sales_point_cashier'])),
      // ])

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
      ->renderHook(
        \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_AFTER,
        fn(): string => Blade::render('
        <div class="flex items-center gap-x-3 ms-4">
            <a href="https://almanzel-alhadith.com/"
               target="_blank"
               title="الذهاب إلى الموقع الإلكتروني"
               class="fi-icon-btn relative flex items-center justify-center rounded-lg p-2
                      text-gray-400 outline-none transition duration-75
                      hover:bg-gray-500/10 hover:text-success-600
                      focus:ring-2 focus:ring-primary-500
                      dark:text-gray-400 dark:hover:bg-gray-500/20 dark:hover:text-success-400"
            >
                <x-heroicon-o-globe-alt class="w-6 h-6 transition-colors duration-200" />
                <span class="hidden md:inline text-sm font-bold ms-1">الموقع</span>
            </a>
        </div>
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
        CapitalStatsWidget::class,
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
