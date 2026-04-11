<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use App\Models\User;

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
        \App\Models\CashierReturnFatora::class,
        \App\Models\CashierSale::class,
        \App\Models\CashierSalesFatora::class,
        \App\Models\CashierSalesReturn::class,
        \App\Models\CompanyEntry::class,
        \App\Models\CompanySalesTransfer::class,
        \App\Models\Order::class,
        \App\Models\SalesPointCashierTrans::class,
        \App\Models\ShippingWarehouse::class,
        \App\Models\WarehouseReturn::class,
        \App\Models\ProductImportItem::class,
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
