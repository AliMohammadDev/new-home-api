<?php

namespace App\Filament\Exports;

use App\Models\ProductImport;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductImportExporter extends Exporter
{
  protected static ?string $model = ProductImport::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('id')->label('رقم تسلسلي'),
      ExportColumn::make('supplier_name')->label('اسم المورد'),
      ExportColumn::make('supplier_phone')->label('رقم هاتف المورد'),
      ExportColumn::make('address')->label('العنوان'),
      ExportColumn::make('notes')->label('ملاحظات'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your product import export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}