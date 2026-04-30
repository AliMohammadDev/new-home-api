<?php

namespace App\Jobs;

use App\Models\CashierReturnFatora;
use App\Models\CashierSale;
use App\Models\CashierSalesFatora;
use App\Models\CashierSalesReturn;
use App\Models\CompanyEntry;
use App\Models\CompanySalesTransfer;
use App\Models\Order;
use App\Models\ProductImportItem;
use App\Models\SalesPointCashierTrans;
use App\Models\ShippingWarehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\WarehouseReturn;

class ArchiveDataJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $user;
  protected $executionTime;

  public function __construct(User $user)
  {
    $this->user = $user;
    $this->executionTime = Carbon::now();
  }

  public function handle(): void
  {
    try {
      DB::beginTransaction();

      $models = [
        CashierReturnFatora::class,
        CashierSale::class,
        CashierSalesFatora::class,
        CashierSalesReturn::class,
        CompanyEntry::class,
        CompanySalesTransfer::class,
        Order::class,
        SalesPointCashierTrans::class,
        ShippingWarehouse::class,
        WarehouseReturn::class,
        ProductImportItem::class,
      ];

      $totalCount = 0;

      foreach ($models as $model) {
        $totalCount += $model::where('created_at', '<=', $this->executionTime)->delete();
      }

      DB::commit();

      Notification::make()
        ->title('اكتملت عملية الأرشفة')
        ->success()
        ->body("تم بنجاح أرشفة ({$totalCount}) سجل.")
        ->sendToDatabase($this->user);

    } catch (\Exception $e) {
      DB::rollBack();

      Notification::make()
        ->title('فشلت عملية الأرشفة')
        ->danger()
        ->body('حدث خطأ أثناء المعالجة: ' . $e->getMessage())
        ->sendToDatabase($this->user);
    }
  }
}
