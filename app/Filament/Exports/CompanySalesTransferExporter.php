<?php

namespace App\Filament\Exports;

use App\Models\CompanySalesTransfer;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CompanySalesTransferExporter extends Exporter
{
  protected static ?string $model = CompanySalesTransfer::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('id')->label('رقم تسلسلي'),
      ExportColumn::make('sales_point_id')->label('رقم معرف نقطةالمبيع'),
      // ExportColumn::make('trans_type')->label('نوع التحويل'),
      ExportColumn::make('trans_type')
        ->label('نوع التحويل')
        ->formatStateUsing(fn(string $state): string => match ($state) {
          'deposit' => 'دائن',
          'withdraw' => 'مدين',
          default => $state,
        }),
      ExportColumn::make('name')->label('اسم البيان'),
      ExportColumn::make('date')->label('التاريخ'),
      ExportColumn::make('quantity')->label('الكمية'),
      ExportColumn::make('note')->label('ملاحظات'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your company sales transfer export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}
