<?php

namespace App\Filament\Exports;

use App\Models\CashierSalesFatora;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CashierSalesFatoraExporter extends Exporter
{
  protected static ?string $model = CashierSalesFatora::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('id')->label('رقم تسلسلي'),
      ExportColumn::make('cashier.user.name')->label('كاشير نقطة المبيع'),
      ExportColumn::make('date')->label('التاريخ'),
      ExportColumn::make('full_price')->label('السعر الإجمالي'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your cashier sales fatora export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}