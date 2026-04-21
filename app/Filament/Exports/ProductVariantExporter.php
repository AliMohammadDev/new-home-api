<?php

namespace App\Filament\Exports;

use App\Models\ProductVariant;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductVariantExporter extends Exporter
{
  protected static ?string $model = ProductVariant::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('id')->label('رقم تسلسلي'),
      ExportColumn::make('product.name')->label('اسم المنتج'),
      ExportColumn::make('color.color')->label('اللون'),
      ExportColumn::make('size.size')->label('الحجم'),
      ExportColumn::make('material.material')->label('المادة الخام'),
      ExportColumn::make('price')->label('السعر'),
      ExportColumn::make('discount')->label('الخصم'),
      ExportColumn::make('stock_quantity')->label('الكمية المتوفرة'),
      ExportColumn::make('sku'),
      ExportColumn::make('barcode'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your product variant export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}