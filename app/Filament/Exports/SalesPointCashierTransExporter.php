<?php

namespace App\Filament\Exports;

use App\Models\SalesPointCashierTrans;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SalesPointCashierTransExporter extends Exporter
{
  protected static ?string $model = SalesPointCashierTrans::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('id')->label('رقم تسلسلي'),
      ExportColumn::make('salesPoint.name')
        ->label('نقطة المبيع'),
      ExportColumn::make('manager.user.name')
        ->label('مدير نقطة المبيع'),
      ExportColumn::make('cashier.user.name')
        ->label('كاشير نقطة المبيع'),
      ExportColumn::make('trans_type')
        ->label('نوع التحويل')
        ->formatStateUsing(fn(string $state): string => match ($state) {
          'deposit' => 'دائن',
          'withdraw' => 'مدين',
          default => $state,
        }),
      ExportColumn::make('name')->label('اسم البيان'),
      ExportColumn::make('date')->label('التاريخ'),
      ExportColumn::make('amount')->label('المبلغ'),
      ExportColumn::make('waste')->label('المهدور'),
      ExportColumn::make('note')->label('ملاحظات'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your sales point cashier trans export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}
