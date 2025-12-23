<?php

namespace App\Providers\Filament;

use App\Filament\Resources\CategoryResource\Widgets\CategoriesCountWidget;
use App\Filament\Resources\OrderResource\Widgets\OrdersCountWidget;
use App\Filament\Resources\ProductResource\Widgets\ProductsCountWidget;
use App\Filament\Resources\UserResource\Widgets\UsersCountWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      ->default()
      ->id('admin')
      ->path('admin')
      ->login()
      // تم حذف contentGrid لأنها تسبب الخطأ هنا
      ->brandName('المنزل الحديث')
      ->brandLogo(asset('images/home-logo-black_dicco2.svg'))
      ->darkModeBrandLogo(asset('images/home-logo-white_c2et5l.svg'))
      ->brandLogoHeight('3rem')
      ->colors([
        'primary' => '#025043',
      ])
      // طريقة صحيحة وآمنة لتكبير الخط في كامل اللوحة
      ->font('Cairo') // اختيار خط عربي ممتاز
      ->renderHook(
        \Filament\View\PanelsRenderHook::HEAD_END,
        fn(): string => \Illuminate\Support\Facades\Blade::render('
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
        UsersCountWidget::class,
        CategoriesCountWidget::class,
        OrdersCountWidget::class,
        ProductsCountWidget::class,
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