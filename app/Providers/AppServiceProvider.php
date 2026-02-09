<?php

namespace App\Providers;

use App\Models\ShippingWarehouse;
use App\Observers\ShippingWarehouseObserver;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    FilamentIcon::register([
      'navigation.category' => 'icon-category',
      'navigation.size' => 'icon-size',
      'navigation.material' => 'icon-material',
      'navigation.product' => 'icon-product',
      'navigation.order' => 'icon-order',
      'navigation.cart' => 'icon-cart',
    ]);
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    ShippingWarehouse::observe(ShippingWarehouseObserver::class);
  }
}