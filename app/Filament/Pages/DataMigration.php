<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class DataMigration extends Page
{
  use HasPageShield;

  protected static ?string $navigationIcon = 'heroicon-o-archive-box';
  protected static ?string $navigationGroup = 'الإعدادات المتقدمة';
  protected static ?string $navigationLabel = 'ترحيل وأرشفة المواد';
  protected static ?string $title = 'ترحيل البيانات الحالي';

  protected static string $view = 'filament.pages.data-migration';

  protected function getHeaderActions(): array
  {
    return [
      Action::make('run_migration')
        ->label('ترحيل كافة البيانات الآن')
        ->icon('heroicon-o-archive-box')
        ->color('warning')
        ->size('xl')
        ->requiresConfirmation()
        ->modalHeading('تأكيد الترحيل الكامل')
        ->modalDescription('عند التأكيد، سيتم أرشفة كافة السجلات الموجودة حالياً في النظام وحتى هذه اللحظة. ستظهر الجداول فارغة بعد العملية. هل أنت متأكد؟')
        ->modalSubmitActionLabel('نعم، أرشفة الكل')
        ->action(fn() => $this->performMigration()),
    ];
  }

  public function performMigration()
  {
    $now = Carbon::now();

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

      $results = [];
      $totalCount = 0;

      foreach ($models as $model) {
        $shortName = (new \ReflectionClass($model))->getShortName();

        $deletedCount = $model::where('created_at', '<=', $now)->delete();

        if ($deletedCount > 0) {
          $results[] = "{$shortName}: {$deletedCount}";
          $totalCount += $deletedCount;
        }
      }

      DB::commit();

      Notification::make()
        ->title('تمت الأرشفة بنجاح')
        ->success()
        ->body("تم نقل ({$totalCount}) سجل إلى الأرشيف بنجاح.")
        ->persistent()
        ->send();

    } catch (\Exception $e) {
      DB::rollBack();

      Notification::make()
        ->title('خطأ في العملية')
        ->danger()
        ->body('حدث خطأ: ' . $e->getMessage())
        ->send();
    }
  }
}